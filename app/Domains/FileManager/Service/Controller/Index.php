<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Service\Controller;

use App\Domains\FileManager\Model\FileManager;
use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;

class Index
{
    protected $request;

    protected $auth;

    public function __construct($request, $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new($request, $auth): self
    {
        return new self($request, $auth);
    }

    public function data(): array
    {
        return [
            'model' => $this->getModel(),
            'tree' => $this->buildTree(),
        ];
    }

    protected function getModel()
    {
        $query = FileManager::query();

        if (!$this->auth->hasRole('root')) {
            $enterpriseId = $this->auth->enterprise_id ?? null;
            if (!$enterpriseId) {
                return [];
            }
            $query->where('enterprise_id', $enterpriseId);
        }

        if ($search = $this->request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%$search%")
                    ->orWhere('model_url', 'like', "%$search%");
            });
        }

        $query->orderBy('created_at', 'asc');

        return $query->get();
    }

    /**
     * Xây dựng cây thư mục từ các model và MinIO.
     *
     * @return array
     */
    protected function buildTree(): array
    {
        $tree = [];
        $enterpriseId = $this->auth->hasRole('root') ? null : $this->auth->enterprise_id;
        $prefix = $enterpriseId ? "{$enterpriseId}/" : '';

        // Initialize S3Client for MinIO
        $config = config('filesystems.disks.minio');
        $s3Client = new S3Client([
            'credentials' => [
                'key' => $config['key'],
                'secret' => $config['secret'],
            ],
            'region' => $config['region'],
            'version' => 'latest',
            'endpoint' => $config['endpoint'],
            'use_path_style_endpoint' => $config['use_path_style_endpoint'] ?? true,
        ]);

        // Step 1: Build tree từ database models
        $models = $this->getModel();
        foreach ($models as $model) {
            $relativePath = $enterpriseId && strpos($model->model_url, "/{$enterpriseId}/") === 0
                ? substr($model->model_url, strlen("/{$enterpriseId}/"))
                : trim($model->model_url, '/');
            if (empty($relativePath)) {
                continue;
            }

            $parts = explode('/', $relativePath);
            $current = &$tree;
            $path = '';

            for ($i = 0; $i < count($parts); $i++) {
                $path .= ($i === 0 ? '' : '/').$parts[$i];
                $isLastPart = $i === count($parts) - 1;
                $isFolder = $model->is_folder || !$isLastPart;

                if (!isset($current[$parts[$i]])) {
                    $current[$parts[$i]] = [
                        'name' => $parts[$i],
                        'children' => [],
                        'file_count' => 0,
                        'is_folder' => $isFolder,
                        'model_id' => ($isFolder && $isLastPart && $model->is_folder && $path === $relativePath) ? $model->id : null,
                    ];
                }

                if ($model->is_folder && $isLastPart && $path === $relativePath) {
                    $current[$parts[$i]]['file_count'] = $model->countFilesInFolder() ?? 0;
                } elseif (!$model->is_folder && $isFolder && $isLastPart && $i > 0) {
                    $parentName = $parts[$i - 1];
                    if (isset($current[$parentName])) {
                        $current[$parentName]['file_count'] = ($current[$parentName]['file_count'] ?? 0) + 1;

                    }
                }

                $current = &$current[$parts[$i]]['children'];
            }
        }

        // Step 2: Fetch all paths from MinIO using listObjectsV2
        $allPaths = [];
        $continuationToken = null;

        try {
            do {
                $params = [
                    'Bucket' => $config['bucket'],
                    'Prefix' => $prefix, // Restrict to enterpriseId prefix
                    'MaxKeys' => 1000,
                ];
                if ($continuationToken) {
                    $params['ContinuationToken'] = $continuationToken;
                }

                $result = $s3Client->listObjectsV2($params);

                if (isset($result['Contents'])) {
                    foreach ($result['Contents'] as $object) {
                        $allPaths[] = $object['Key'];
                    }
                }

                $continuationToken = $result['IsTruncated'] ? $result['NextContinuationToken'] : null;
            } while ($continuationToken);
        } catch (\Exception $e) {
            Log::error('Lỗi đồng bộ MinIO: '.$e->getMessage());
        }

        // Step 3: Merge MinIO paths into the tree and sync with database
        foreach ($allPaths as $objectPath) {
            $relativePath = $enterpriseId ? str_replace("{$enterpriseId}/", '', $objectPath) : $objectPath;
            $relativePath = trim($relativePath, '/');
            if (empty($relativePath)) {
                continue;
            }

            $parts = explode('/', $relativePath);
            $current = &$tree;
            $path = '';

            for ($i = 0; $i < count($parts); $i++) {
                $path .= ($i === 0 ? '' : '/').$parts[$i];
                $isLastPart = $i === count($parts) - 1;
                $isFolder = !$isLastPart || (substr($objectPath, -1) === '/'); // Folders end with '/'

                $modelUrl = "/{$prefix}{$path}";
                $model = FileManager::where('model_url', $modelUrl)->first();

                // If the path doesn't exist in the database, create it
                if (!$model && $isLastPart) {
                    $parentPath = $i > 0 ? "/{$prefix}".implode('/', array_slice($parts, 0, $i)) : null;
                    $modelData = [
                        'name' => $parts[$i],
                        'file_name' => $parts[$i],
                        'model_url' => $modelUrl,
                        'size' => $isFolder ? 0 : ($s3Client->headObject(['Bucket' => $config['bucket'], 'Key' => $objectPath])['ContentLength'] ?? 0),
                        'type' => $isFolder ? null : ($s3Client->headObject(['Bucket' => $config['bucket'], 'Key' => $objectPath])['ContentType'] ?? 'application/octet-stream'),
                        'enterprise_id' => $enterpriseId,
                        'parent_id' => $parentPath ? FileManager::where('model_url', $parentPath)->first()?->id : null,
                        'is_folder' => $isFolder,
                    ];
                    $model = FileManager::create($modelData);
                }

                if (!isset($current[$parts[$i]])) {
                    $current[$parts[$i]] = [
                        'name' => $parts[$i],
                        'children' => [],
                        'file_count' => $isFolder && $model ? $model->countFilesInFolder() ?? 0 : 0,
                        'is_folder' => $isFolder,
                        'model_id' => $model ? $model->id : null,
                    ];
                }

                // Update file_count for parent folder when processing a file
                if (!$isFolder && $isLastPart && $i > 0) {
                    $parentName = $parts[$i - 1];
                    if (isset($current[$parentName])) {
                        $current[$parentName]['file_count'] = ($current[$parentName]['file_count'] ?? 0) + 1;
                    }
                }

                $current = &$current[$parts[$i]]['children'];
            }
        }

        return $tree;
    }

    /**
     * Lấy danh sách các thư mục con của một đường dẫn cụ thể.
     *
     * @param string $path
     * @return \Illuminate\Support\Collection
     */
    public function getChildren($path)
    {
        $enterpriseId = $this->auth->hasRole('root') ? null : $this->auth->enterprise_id;
        $prefix = $enterpriseId ? "{$enterpriseId}/" : '';
        $fullPath = '/'.trim(($enterpriseId ? $enterpriseId : '').'/'.trim($path, '/'), '/');

        $query = FileManager::query();
        if (!$this->auth->hasRole('root')) {
            if (!$enterpriseId) {
                return collect([]);
            }
            $query->where('enterprise_id', $enterpriseId);
        }

        $query->where('model_url', 'like', $fullPath.'/%')
            ->whereRaw('model_url NOT LIKE ?', [$fullPath.'/%/%']) // Chỉ lấy con trực tiếp
            ->orWhere('model_url', $fullPath); // Bao gồm chính folder hiện tại

        $dbItems = $query->get();

        // Fetch files from MinIO
        $config = config('filesystems.disks.minio');
        $s3Client = new S3Client([
            'credentials' => [
                'key' => $config['key'],
                'secret' => $config['secret'],
            ],
            'region' => $config['region'],
            'version' => 'latest',
            'endpoint' => $config['endpoint'],
            'use_path_style_endpoint' => $config['use_path_style_endpoint'] ?? true,
        ]);

        $minioItems = [];
        try {
            $result = $s3Client->listObjectsV2([
                'Bucket' => $config['bucket'],
                'Prefix' => trim($fullPath, '/').'/', // Thêm '/' để lấy con trực tiếp
                'Delimiter' => '/', // Chỉ lấy con trực tiếp
            ]);

            // Process folders
            if (isset($result['CommonPrefixes'])) {
                foreach ($result['CommonPrefixes'] as $prefix) {
                    $relativePath = trim(str_replace($prefix, '', $prefix['Prefix']), '/');
                    $name = basename($relativePath);
                    if ($name && !$dbItems->contains('model_url', "/{$prefix['Prefix']}")) {
                        $model = FileManager::create([
                            'name' => $name,
                            'file_name' => null,
                            'model_url' => "/{$prefix['Prefix']}",
                            'size' => 0,
                            'type' => null,
                            'enterprise_id' => $enterpriseId,
                            'parent_id' => FileManager::where('model_url', $fullPath)->first()?->id,
                            'is_folder' => true,
                        ]);
                        $minioItems[] = $model;
                    }
                }
            }

            // Process files
            if (isset($result['Contents'])) {
                foreach ($result['Contents'] as $object) {
                    $objectPath = $object['Key'];
                    if ($objectPath === trim($fullPath, '/').'/') {
                        continue; // Bỏ qua chính thư mục hiện tại
                    }
                    $relativePath = trim(str_replace($prefix, '', $objectPath), '/');
                    if (dirname($relativePath) !== trim(str_replace($prefix, '', $fullPath), '/')) {
                        continue; // Chỉ lấy file trực tiếp
                    }
                    $name = basename($relativePath);
                    if ($name && !$dbItems->contains('model_url', "/{$objectPath}")) {
                        $model = FileManager::create([
                            'name' => pathinfo($name, PATHINFO_FILENAME),
                            'file_name' => $name,
                            'model_url' => "/{$objectPath}",
                            'size' => $object['Size'] ?? 0,
                            'type' => $s3Client->headObject(['Bucket' => $config['bucket'], 'Key' => $objectPath])['ContentType'] ?? 'application/octet-stream',
                            'enterprise_id' => $enterpriseId,
                            'parent_id' => FileManager::where('model_url', $fullPath)->first()?->id,
                            'is_folder' => false,
                        ]);
                        $minioItems[] = $model;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Lỗi lấy file từ MinIO: '.$e->getMessage());
        }

        // Combine database and MinIO items
        return $dbItems->merge($minioItems);
    }
}
