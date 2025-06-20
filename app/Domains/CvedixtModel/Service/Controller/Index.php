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

        if ($this->auth->hasRole('root')) {
            $query->withTrashed();
        } else {
            $enterpriseId = $this->auth->enterprise_id ?? null;
            if (!$enterpriseId) {
                return [];
            }
            $query->where('enterprise_id', $enterpriseId)->whereNull('deleted_at');
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
                        'deleted_at' => $model->deleted_at ? $model->deleted_at->timestamp : null,
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
            // Lấy tất cả thư mục từ MinIO
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
                            'deleted_at' => $model && $model->deleted_at ? $model->deleted_at->timestamp : null,
                        ];
                    }
                    $current = &$current[$parts[$i]]['children'];
                }
            }

            // Lấy danh sách file từ MinIO
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
                                'deleted_at' => $model && $model->deleted_at ? $model->deleted_at->timestamp : null,
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
                                'deleted_at' => $model && $model->deleted_at ? $model->deleted_at->timestamp : null,
                            ];
                        }
                        $current = &$current[$parts[$i]]['children'];
                    }
                    $path .= ($i === 0 ? '' : '/').$parts[$i];
                }
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi đồng bộ MinIO: ' . $e->getMessage());
        }

        return $tree;
    }

    public function getChildren($path)
    {
        $query = CvedixtModel::query();

        if ($this->auth->hasRole('root')) {
            $query->withTrashed();
        } else {
            $enterpriseId = $this->auth->enterprise_id ?? null;
            if (!$enterpriseId) {
                return collect([]);
            }
            $query->where('enterprise_id', $enterpriseId)->whereNull('deleted_at');
        }

        $fullPath = ($this->auth->hasRole('root') ? '' : ($this->auth->enterprise_id ?? '')).'/'.trim($path, '/');
        $fullPath = '/'.trim($fullPath, '/');

        // Lọc các bản ghi là con trực tiếp
        $query->where('parent_id', function ($q) use ($fullPath) {
            $q->select('id')->from('cvedixt_model')->where('model_url', $fullPath);
        })->orWhere(function ($q) use ($fullPath) {
            $q->where('model_url', $fullPath)->where('is_folder', true);
        });

        return $query->get();
    }

    public function createFolder(): \Illuminate\Http\JsonResponse
    {
        try {
            $parentPath = trim($this->request->input('parent_id', ''));
            $folderName = trim($this->request->input('name'));
            if (empty($folderName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên thư mục không được để trống',
                ], 422);
            }

            // Kiểm tra ký tự không hợp lệ
            if (preg_match('/[<>:"\/\\|?*]/', $folderName)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tên thư mục chứa ký tự không hợp lệ',
                ], 422);
            }

            $enterpriseId = $this->auth->hasRole('root') ? null : ($this->auth->enterprise_id ?? null);
            if (!$enterpriseId && !$this->auth->hasRole('root')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Không có quyền tạo thư mục',
                ], 403);
            }

            // Xây dựng đường dẫn đầy đủ
            $basePath = $parentPath ? ($enterpriseId ? "{$enterpriseId}/{$parentPath}" : $parentPath) : ($enterpriseId ? $enterpriseId : '');
            $path = $basePath ? "{$basePath}/{$folderName}" : $folderName;
            $fullPath = '/'.trim($path, '/');

            // Kiểm tra thư mục đã tồn tại
            if (Storage::disk('minio')->exists($path) || CvedixtModel::where('model_url', $fullPath)->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Thư mục đã tồn tại',
                ], 422);
            }

            // Tạo thư mục trên MinIO
            Storage::disk('minio')->makeDirectory($path);

            // Tìm parent_id
            $parentId = null;
            if ($parentPath) {
                $parentFullPath = '/'.trim($enterpriseId ? "{$enterpriseId}/{$parentPath}" : $parentPath, '/');
                $parentModel = CvedixtModel::where('model_url', $parentFullPath)->first();
                if ($parentModel) {
                    $parentId = $parentModel->id;
                } else {
                    Log::warning("Không tìm thấy thư mục cha với model_url: {$parentFullPath}");
                }
            }

            // Lưu vào database
            $model = CvedixtModel::create([
                'name' => $folderName,
                'file_name' => $folderName,
                'model_url' => $fullPath,
                'size' => 0,
                'type' => 'folder',
                'enterprise_id' => $enterpriseId,
                'parent_id' => $parentId,
                'is_folder' => true,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Tạo thư mục thành công',
                'data' => [
                    'path' => $path,
                    'fullPath' => $fullPath,
                    'name' => $folderName,
                    'parentPath' => $parentPath,
                    'model_id' => $model->id,
                ],
            ], 201);
        } catch (\Exception $e) {
            Log::error('Lỗi khi tạo thư mục: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Lỗi khi tạo thư mục: ' . $e->getMessage(),
            ], 500);
        }
    }
}
