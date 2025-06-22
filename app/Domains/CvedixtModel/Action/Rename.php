<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Action;

use App\Domains\CvedixtModel\Model\CvedixtModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class Rename
{
    public function handle(int $modelId, string $newName, $user): array
    {
        try {
            $model = CvedixtModel::findOrFail($modelId);

            if (!$user->hasRole('root') && $model->enterprise_id !== $user->enterprise_id) {
                return [
                    'success' => false,
                    'message' => __('model-rename.no-permission'),
                ];
            }

            if (preg_match('/[<>:"\/\\|?*]/', $newName)) {
                return [
                    'success' => false,
                    'message' => 'Tên mới chứa ký tự không hợp lệ',
                ];
            }

            $data = ['name' => $newName];
            if ($model->is_folder) {
                // Sửa đường dẫn để bao gồm enterprise_id
                $enterpriseId = $model->enterprise_id ?? 'default';
                $oldPath = ltrim($model->model_url, '/'); // Ví dụ: 2/.NET 22222
                $newPath = dirname($model->model_url) . '/' . $newName;
                $newPath = '/' . trim($newPath, '/'); // Lưu vào DB: /2/.NET 22222_New
                $minioNewPath = ltrim($newPath, '/'); // Dùng cho MinIO: 2/.NET 22222_New

                // Kiểm tra đường dẫn cũ có tồn tại không
                Log::info('Kiểm tra đường dẫn cũ trên MinIO', [
                    'model_id' => $modelId,
                    'old_path' => $oldPath,
                ]);
                if (!Storage::disk('minio')->exists($oldPath)) {
                    Log::error('Thư mục cũ không tồn tại trên MinIO', [
                        'model_id' => $modelId,
                        'old_path' => $oldPath,
                    ]);
                    // Tạo thư mục nếu chưa tồn tại
                    Storage::disk('minio')->makeDirectory($oldPath);
                    Log::info('Đã tạo thư mục cũ trên MinIO', ['old_path' => $oldPath]);
                }

                // Kiểm tra tên mới đã tồn tại chưa
                if (Storage::disk('minio')->exists($minioNewPath) || CvedixtModel::where('model_url', $newPath)->exists()) {
                    return [
                        'success' => false,
                        'message' => 'Tên thư mục đã tồn tại',
                    ];
                }

                // Đổi tên trên MinIO
                Log::info('Đổi tên thư mục trên MinIO', [
                    'model_id' => $modelId,
                    'old_path' => $oldPath,
                    'new_path' => $minioNewPath,
                ]);
                $moved = Storage::disk('minio')->move($oldPath, $minioNewPath);
                if (!$moved) {
                    Log::error('Không thể đổi tên thư mục trên MinIO', [
                        'model_id' => $modelId,
                        'old_path' => $oldPath,
                        'new_path' => $minioNewPath,
                    ]);
                    return [
                        'success' => false,
                        'message' => 'Lỗi khi đổi tên thư mục trên MinIO',
                    ];
                }

                $data['model_url'] = $newPath;
                $data['file_name'] = $newName;

                // Cập nhật bản ghi chính
                $model->update($data);
                Log::info('Đã cập nhật bản ghi chính', [
                    'model_id' => $modelId,
                    'new_name' => $newName,
                    'new_model_url' => $newPath,
                ]);

                // Cập nhật model_url của các con
                $children = CvedixtModel::where('model_url', 'like', $model->model_url . '/%')->get();
                Log::info('Cập nhật model_url cho các con', [
                    'parent_id' => $modelId,
                    'child_count' => $children->count(),
                ]);
                foreach ($children as $child) {
                    $newChildPath = str_replace($model->model_url, $newPath, $child->model_url);
                    $child->update([
                        'model_url' => $newChildPath,
                    ]);
                    Log::info('Đã cập nhật child', [
                        'child_id' => $child->id,
                        'new_model_url' => $newChildPath,
                    ]);
                }
            } else {
                $extension = pathinfo($model->file_name, PATHINFO_EXTENSION);
                $data['file_name'] = $newName . ($extension ? '.' . $extension : '');
                $newPath = dirname($model->model_url) . '/' . $data['file_name'];
                $newPath = '/' . trim($newPath, '/');
                $minioNewPath = ltrim($newPath, '/');
                $oldPath = ltrim($model->model_url, '/');

                Log::info('Kiểm tra file cũ trên MinIO', [
                    'model_id' => $modelId,
                    'old_path' => $oldPath,
                ]);
                if (!Storage::disk('minio')->exists($oldPath)) {
                    Log::error('File cũ không tồn tại trên MinIO', [
                        'model_id' => $modelId,
                        'old_path' => $oldPath,
                    ]);
                    return [
                        'success' => false,
                        'message' => 'File cũ không tồn tại trên MinIO',
                    ];
                }

                if (Storage::disk('minio')->exists($minioNewPath) || CvedixtModel::where('model_url', $newPath)->exists()) {
                    return [
                        'success' => false,
                        'message' => 'Tên file đã tồn tại',
                    ];
                }

                Log::info('Đổi tên file trên MinIO', [
                    'model_id' => $modelId,
                    'old_path' => $oldPath,
                    'new_path' => $minioNewPath,
                ]);
                $moved = Storage::disk('minio')->move($oldPath, $minioNewPath);
                if (!$moved) {
                    Log::error('Không thể đổi tên file trên MinIO', [
                        'model_id' => $modelId,
                        'old_path' => $oldPath,
                        'new_path' => $minioNewPath,
                    ]);
                    return [
                        'success' => false,
                        'message' => 'Lỗi khi đổi tên file trên MinIO',
                    ];
                }

                $data['model_url'] = $newPath;
                $model->update($data);
                Log::info('Đã cập nhật bản ghi chính', [
                    'model_id' => $modelId,
                    'new_name' => $newName,
                    'new_model_url' => $newPath,
                ]);
            }

            return [
                'success' => true,
                'message' => __('cvedixrt-model.rename.success'),
            ];
        } catch (Exception $e) {
            Log::error('Lỗi khi đổi tên', [
                'model_id' => $modelId,
                'new_name' => $newName,
                'error' => $e->getMessage(),
            ]);
            return [
                'success' => false,
                'message' => __('model-rename.rename-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }
}
