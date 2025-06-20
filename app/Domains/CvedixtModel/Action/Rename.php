<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Action;

use App\Domains\CvedixtModel\Model\CvedixtModel;
use Illuminate\Support\Facades\Storage;
use Exception;
use Illuminate\Support\Facades\Log;

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
                $newPath = dirname($model->model_url).'/'.$newName;
                $newPath = '/'.trim($newPath, '/');
                if (Storage::disk('minio')->exists($newPath) || CvedixtModel::where('model_url', $newPath)->exists()) {
                    return [
                        'success' => false,
                        'message' => 'Tên thư mục đã tồn tại',
                    ];
                }

                Storage::disk('minio')->move(ltrim($model->model_url, '/'), $newPath);
                $data['model_url'] = $newPath;
                $data['file_name'] = $newName;

                // Cập nhật model_url của các con
                $children = CvedixtModel::where('model_url', 'like', $model->model_url.'/%')->get();
                foreach ($children as $child) {
                    $child->update([
                        'model_url' => str_replace($model->model_url, $newPath, $child->model_url),
                    ]);
                }
            } else {
                $extension = pathinfo($model->file_name, PATHINFO_EXTENSION);
                $data['file_name'] = $newName.($extension ? '.'.$extension : '');
                $newPath = dirname($model->model_url).'/'.$data['file_name'];
                $newPath = '/'.trim($newPath, '/');

                if (Storage::disk('minio')->exists($newPath) || CvedixtModel::where('model_url', $newPath)->exists()) {
                    return [
                        'success' => false,
                        'message' => 'Tên file đã tồn tại',
                    ];
                }

                Storage::disk('minio')->move(ltrim($model->model_url, '/'), $newPath);
                $data['model_url'] = $newPath;
            }

            $model->update($data);

            return [
                'success' => true,
                'message' => __('cvedixrt-model.rename.success'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('model-rename.rename-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }
}
