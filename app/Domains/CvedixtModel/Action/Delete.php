<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Action;

use App\Domains\CvedixtModel\Model\CvedixtModel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class Delete
{
    public function handle(int $modelId, $user): array
    {
        try {
            if ($modelId <= 0) {
                Log::error('ID không hợp lệ', ['model_id' => $modelId]);
                return [
                    'success' => false,
                    'message' => 'ID không hợp lệ',
                ];
            }

            $model = CvedixtModel::findOrFail($modelId);
            Log::info('Tìm thấy bản ghi', [
                'model_id' => $modelId,
                'model_url' => $model->model_url,
                'is_folder' => $model->is_folder,
            ]);

            if (!$user->hasRole('root') && $model->enterprise_id !== $user->enterprise_id) {
                Log::error('Không có quyền xóa', ['model_id' => $modelId, 'user_id' => $user->id]);
                return [
                    'success' => false,
                    'message' => __('model-delete.no-permission'),
                ];
            }

            $path = ltrim($model->model_url, '/');
            Log::info('Chuẩn bị xóa trên MinIO', ['model_id' => $modelId, 'path' => $path]);

            if ($model->is_folder) {
                // Kiểm tra và xóa thư mục trên MinIO
                if (Storage::disk('minio')->exists($path)) {
                    Log::info('Xóa thư mục trên MinIO', ['model_id' => $modelId, 'path' => $path]);
                    $deleted = Storage::disk('minio')->deleteDirectory($path);
                    if (!$deleted) {
                        Log::error('Không thể xóa thư mục trên MinIO', ['model_id' => $modelId, 'path' => $path]);
                        return [
                            'success' => false,
                            'message' => 'Lỗi khi xóa thư mục trên MinIO',
                        ];
                    }
                } else {
                    Log::warning('Thư mục không tồn tại trên MinIO', ['model_id' => $modelId, 'path' => $path]);
                }

                // Xóa vĩnh viễn các bản ghi con trong database
                CvedixtModel::where('model_url', 'like', $model->model_url . '/%')->delete();
                Log::info('Đã xóa vĩnh viễn các bản ghi con', ['model_id' => $modelId]);
            } else {
                // Kiểm tra và xóa file trên MinIO
                if (Storage::disk('minio')->exists($path)) {
                    Log::info('Xóa file trên MinIO', ['model_id' => $modelId, 'path' => $path]);
                    $deleted = Storage::disk('minio')->delete($path);
                    if (!$deleted) {
                        Log::error('Không thể xóa file trên MinIO', ['model_id' => $modelId, 'path' => $path]);
                        return [
                            'success' => false,
                            'message' => 'Lỗi khi xóa file trên MinIO',
                        ];
                    }
                } else {
                    Log::warning('File không tồn tại trên MinIO', ['model_id' => $modelId, 'path' => $path]);
                }
            }

            // Xóa vĩnh viễn bản ghi chính trong database
            $model->delete();
            Log::info('Đã xóa vĩnh viễn bản ghi chính', [
                'model_id' => $modelId,
                'model_url' => $model->model_url,
            ]);

            return [
                'success' => true,
                'message' => __('cvedixrt-model.delete.success'),
            ];
        } catch (Exception $e) {
            Log::error('Lỗi khi xóa', [
                'model_id' => $modelId,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);
            return [
                'success' => false,
                'message' => __('model-delete.delete-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }
}
