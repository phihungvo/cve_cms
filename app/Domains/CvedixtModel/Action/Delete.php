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
                return [
                    'success' => false,
                    'message' => 'ID không hợp lệ',
                ];
            }

            $model = CvedixtModel::findOrFail($modelId);

            if (!$user->hasRole('root') && $model->enterprise_id !== $user->enterprise_id) {
                return [
                    'success' => false,
                    'message' => __('cvedixrt-model.no_permission'),
                ];
            }

            $path = ltrim($model->model_url, '/');

            if ($model->is_folder) {
                // Kiểm tra và xóa thư mục trên MinIO
                if (Storage::disk('minio')->exists($path)) {
                    $deleted = Storage::disk('minio')->deleteDirectory($path);
                    if (!$deleted) {
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
            } else {
                // Kiểm tra và xóa file trên MinIO
                if (Storage::disk('minio')->exists($path)) {
                    $deleted = Storage::disk('minio')->delete($path);
                    if (!$deleted) {
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

            return [
                'success' => true,
                'message' => __('cvedixrt-model.delete.success'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('model-delete.delete-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }
}
