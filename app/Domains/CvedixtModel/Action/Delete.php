<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Action;

use App\Domains\CvedixtModel\Model\CvedixtModel;
use Exception;
use Illuminate\Support\Facades\Storage;

class Delete
{
    public function handle($modelUrl, $user): array
    {
        try {
            $model = CvedixtModel::where('model_url', $modelUrl)->first();

            if (!$model) {
                return [
                    'success' => false,
                    'message' => __('cvedixrt-model.delete.not_found'),
                ];
            }

            // Kiểm tra quyền (người không phải root chỉ xóa mềm media của enterprise mình)
            if (!$user->hasRole('root') && $model->enterprise_id !== $user->enterprise_id) {
                return [
                    'success' => false,
                    'message' => __('cvedixrt-model.no_permission'),
                ];
            }

            // Thực hiện soft delete
            $model->delete();

            return [
                'success' => true,
                'message' => __('cvedixrt-model.delete.success'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('cvedixrt-model.delete.error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }

    // Action force delete (dành cho root)
    public function forceDelete($modelId, $user): array
    {
        try {
            if (!$user->hasRole('root')) {
                return [
                    'success' => false,
                    'message' => __('cvedixrt-model.no_permission'),
                ];
            }

            $model = CvedixtModel::withTrashed()->findOrFail($modelId);

            // Xóa file từ MinIO
            $parsedUrl = parse_url($model->model_url, PHP_URL_PATH);
            $filePath = ltrim($parsedUrl, '/');
            $bucketName = config('filesystems.disks.minio.bucket');
            $filePath = str_replace($bucketName.'/', '', $filePath);

            if (Storage::disk('minio')->exists($filePath)) {
                Storage::disk('minio')->delete($filePath);
            }

            // Xóa hoàn toàn bản ghi từ database
            $model->forceDelete();

            return [
                'success' => true,
                'message' => __('cvedixrt-model.force_delete.success'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('cvedixrt-model.force_delete.error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }

    // Action restore (dành cho root)
    public function restore($modelId, $user): array
    {
        try {
            if (!$user->hasRole('root')) {
                return [
                    'success' => false,
                    'message' => __('cvedixrt-model.no_permission'),
                ];
            }

            $model = CvedixtModel::withTrashed()->findOrFail($modelId);
            $model->restore();

            return [
                'success' => true,
                'message' => __('cvedixrt-model.restore.success'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('cvedixrt-model.restore.error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }
}
