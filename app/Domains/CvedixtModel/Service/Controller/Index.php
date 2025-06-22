<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Service\Controller;

use App\Domains\CvedixtModel\Model\CvedixtModel;
use Illuminate\Support\Facades\Storage;
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
        $query = CvedixtModel::query();

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

    protected function buildTree()
    {
        $tree = [];
        $enterpriseId = $this->auth->hasRole('root') ? null : $this->auth->enterprise_id;
        $prefix = $enterpriseId ? "{$enterpriseId}/" : '';

        // Xây dựng cây từ database
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
                        'children' => [],
                        'file_count' => 0,
                        'is_folder' => $isFolder,
                        'model_id' => ($isFolder && $isLastPart && $model->is_folder && $path === $relativePath) ? $model->id : null,
                    ];
                }

                if ($model->is_folder && $isLastPart && $path === $relativePath) {
                    $current[$parts[$i]]['file_count'] = $model->countFilesInFolder();
                } elseif (!$model->is_folder && $isLastPart && $i > 0) {
                    $parentName = $parts[$i - 1];
                    if (isset($current[$parentName])) {
                        $current[$parentName]['file_count'] = ($current[$parentName]['file_count'] ?? 0) + 1;
                    }
                }

                $current = &$current[$parts[$i]]['children'];
            }
        }

        // Đồng bộ với MinIO
        $disk = Storage::disk('minio');
        try {
            $objects = $disk->allDirectories($prefix);
            foreach ($objects as $objectPath) {
                $relativePath = $enterpriseId ? str_replace("{$enterpriseId}/", '', $objectPath) : $objectPath;
                $parts = explode('/', trim($relativePath, '/'));
                if (empty($parts[0])) {
                    continue;
                }

                $current = &$tree;
                $path = '';

                for ($i = 0; $i < count($parts); $i++) {
                    $path .= ($i === 0 ? '' : '/').$parts[$i];
                    if (!isset($current[$parts[$i]])) {
                        $model = CvedixtModel::where('model_url', "/{$prefix}{$path}")->first();
                        $current[$parts[$i]] = [
                            'children' => [],
                            'file_count' => $model ? $model->countFilesInFolder() : 0,
                            'is_folder' => true,
                            'model_id' => $model ? $model->id : null,
                        ];
                    }
                    $current = &$current[$parts[$i]]['children'];
                }
            }

            $files = $disk->files($prefix);
            foreach ($files as $filePath) {
                $relativePath = $enterpriseId ? str_replace("{$enterpriseId}/", '', $filePath) : $filePath;
                $parts = explode('/', trim($relativePath, '/'));
                if (empty($parts[0])) {
                    continue;
                }

                $current = &$tree;

                for ($i = 0; $i < count($parts); $i++) {
                    $isLastPart = $i === count($parts) - 1;
                    if ($isLastPart) {
                        $fileName = $parts[$i];
                        if (!isset($current[$fileName])) {
                            $model = CvedixtModel::where('model_url', "/{$prefix}{$relativePath}")->first();
                            $current[$fileName] = [
                                'children' => [],
                                'file_count' => 0,
                                'is_folder' => false,
                                'model_id' => $model ? $model->id : null,
                            ];
                        }
                        if ($i > 0) {
                            $parentName = $parts[$i - 1];
                            if (isset($current[$parentName])) {
                                $current[$parentName]['file_count'] = ($current[$parentName]['file_count'] ?? 0) + 1;
                            }
                        }
                    } else {
                        if (!isset($current[$parts[$i]])) {
                            $model = CvedixtModel::where('model_url', "/{$prefix}{$path}")->first();
                            $current[$parts[$i]] = [
                                'children' => [],
                                'file_count' => $model ? $model->countFilesInFolder() : 0,
                                'is_folder' => true,
                                'model_id' => $model ? $model->id : null,
                            ];
                        }
                        $current = &$current[$parts[$i]]['children'];
                    }
                    $path .= ($i === 0 ? '' : '/').$parts[$i];
                }
            }
        } catch (\Exception $e) {
            Log::error('Lỗi đồng bộ MinIO: ' . $e->getMessage());
        }

        return $tree;
    }

    public function getChildren($path)
    {
        $query = CvedixtModel::query();

        if (!$this->auth->hasRole('root')) {
            $enterpriseId = $this->auth->enterprise_id ?? null;
            if (!$enterpriseId) {
                return collect([]);
            }
            $query->where('enterprise_id', $enterpriseId);
        }

        // Xử lý đường dẫn đầy đủ
        $fullPath = '/'.trim(($this->auth->hasRole('root') ? '' : ($this->auth->enterprise_id ?? '')).'/'.trim($path, '/'), '/');

        // Lấy tất cả bản ghi con trực tiếp (file và folder) trong subfolder
        $query->where('model_url', 'like', $fullPath.'/%')
            ->whereRaw('model_url NOT LIKE ?', [$fullPath.'/%/%']) // Chỉ lấy con trực tiếp
            ->orWhere('model_url', $fullPath); // Bao gồm chính folder hiện tại

        return $query->get();
    }
}
