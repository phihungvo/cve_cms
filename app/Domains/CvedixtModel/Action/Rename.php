<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Action;

use App\Domains\CvedixtModel\Model\CvedixtModel;
use Exception;
use Illuminate\Support\Facades\Log;

class Rename
{
    public function handle($modelId, $newName, $user): array
    {
        try {
            $model = CvedixtModel::find($modelId);

            if (!$model) {
                return [
                    'success' => false,
                    'message' => __('model-rename.rename-error-not-found'),
                ];
            }

            // Kiểm tra quyền: Root hoặc người dùng thuộc enterprise của model
            if (!$user->hasRole('root') && $model->enterprise_id !== $user->enterprise_id) {
                return [
                    'success' => false,
                    'message' => __('model-rename.no-permission'),
                ];
            }

            // Chỉ cập nhật tên trong database, không thay đổi file trên MinIO
            $model->update([
                'name' => $newName,
            ]);

            return [
                'success' => true,
                'message' => __('model-rename.rename-success'),
            ];
        } catch (Exception $e) {
            Log::error('Error renaming model: ', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => __('model-rename.rename-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }
}
