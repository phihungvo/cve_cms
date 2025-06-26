<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Action;

use App\Domains\FileManager\Model\FileManager;
use Illuminate\Support\Facades\Storage;
use Exception;

class DeleteAction
{
    /**
     * Xử lý xóa file hoặc thư mục từ MinIO và database.
     *
     * @param int $modelId ID của file hoặc thư mục cần xóa
     * @param mixed $user Người dùng thực hiện hành động
     * @return array Kết quả của hành động xóa
     */
    public function handle(int $modelId, $user): array
    {
        try {
            if ($modelId <= 0) {
                return [
                    'success' => false,
                    'message' => 'ID không hợp lệ',
                ];
            }

            $model = FileManager::findOrFail($modelId);

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
                    throw new Exception('Thư mục không tồn tại trên MinIO: '.$path, 404);
                }

                // Xóa vĩnh viễn các bản ghi con trong database
                FileManager::where('model_url', 'like', $model->model_url.'/%')->delete();
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
                    throw new Exception('File không tồn tại trên MinIO: '.$path, 404);
                }
            }

            // Xóa vĩnh viễn bản ghi chính trong database
            $model->delete();

            return [
                'success' => true,
                'message' => __('file-manager.delete.success'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('file-manager.delete.error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }
}
