<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Action;

use App\Domains\CvedixtModel\Model\CvedixtModel;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;

class Delete
{
    public function handle(int $id, $user): array
    {
        try {
            $model = CvedixtModel::findOrFail($id);

            if (!$user->hasRole('root') && $model->enterprise_id !== $user->enterprise_id) {
                return [
                    'success' => false,
                    'message' => __('model-delete.no-permission'),
                ];
            }

            if ($model->is_folder) {
                if ($model->children()->exists()) {
                    return [
                        'success' => false,
                        'message' => __('model-delete.folder-not-empty'),
                    ];
                }
                Storage::disk('minio')->deleteDirectory(ltrim($model->model_url, '/'));
            } else {
                Storage::disk('minio')->delete(ltrim($model->model_url, '/'));
            }

            $model->delete();

            return [
                'success' => true,
                'message' => __('model-delete.success'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('model-delete.error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }

    public function restore(int $id, $user): array
    {
        try {
            $model = CvedixtModel::withTrashed()->findOrFail($id);

            if (!$user->hasRole('root') && $model->enterprise_id !== $user->enterprise_id) {
                return [
                    'success' => false,
                    'message' => __('model-restore.no-permission'),
                ];
            }

            $model->restore();

            return [
                'success' => true,
                'message' => __('model-restore.success'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('model-restore.error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }

    public function forceDelete(int $id, $user): array
    {
        try {
            $model = CvedixtModel::withTrashed()->findOrFail($id);

            if (!$user->hasRole('root') && $model->enterprise_id !== $user->enterprise_id) {
                return [
                    'success' => false,
                    'message' => __('model-force-delete.no-permission'),
                ];
            }

            if ($model->is_folder) {
                if ($model->children()->exists()) {
                    return [
                        'success' => false,
                        'message' => __('model-force-delete.folder-not-empty'),
                    ];
                }
                Storage::disk('minio')->deleteDirectory(ltrim($model->model_url, '/'));
            } else {
                Storage::disk('minio')->delete(ltrim($model->model_url, '/'));
            }

            $model->forceDelete();

            return [
                'success' => true,
                'message' => __('model-force-delete.success'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('model-force-delete.error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }
}
