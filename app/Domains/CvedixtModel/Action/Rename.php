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
                    'message' => __('cvedixrt-model.no_permission'),
                ];
            }

            // Kiểm tra xem tên mới có hợp lệ không
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
                $newPath = dirname($model->model_url).'/'.$newName;
                $newPath = '/'.trim($newPath, '/'); // Lưu vào DB: /2/.NET 22222_New
                $minioNewPath = ltrim($newPath, '/'); // Dùng cho MinIO: 2/.NET 22222_New

                // Nếu thư mục cũ không tồn tại, tạo nó
                if (!Storage::disk('minio')->exists($oldPath)) {
                    // Tạo thư mục nếu chưa tồn tại
                    Storage::disk('minio')->makeDirectory($oldPath);
                }

                // Kiểm tra tên mới đã tồn tại chưa
                if (Storage::disk('minio')->exists($minioNewPath) || CvedixtModel::where('model_url', $newPath)->exists()) {
                    return [
                        'success' => false,
                        'message' => 'Tên thư mục đã tồn tại',
                    ];
                }

                // Đổi tên trên MinIO
                $moved = Storage::disk('minio')->move($oldPath, $minioNewPath);
                if (!$moved) {
                    return [
                        'success' => false,
                        'message' => 'Lỗi khi đổi tên thư mục trên MinIO',
                    ];
                }

                $data['model_url'] = $newPath;
                $data['file_name'] = $newName;

                // Cập nhật bản ghi chính
                $model->update($data);

                // Cập nhật model_url của các con
                $children = CvedixtModel::where('model_url', 'like', $model->model_url.'/%')->get();

                foreach ($children as $child) {
                    $newChildPath = str_replace($model->model_url, $newPath, $child->model_url);
                    $child->update([
                        'model_url' => $newChildPath,
                    ]);
                }
            } else {
                $extension = pathinfo($model->file_name, PATHINFO_EXTENSION);
                $data['file_name'] = $newName.($extension ? '.'.$extension : '');
                $newPath = dirname($model->model_url).'/'.$data['file_name'];
                $newPath = '/'.trim($newPath, '/');
                $minioNewPath = ltrim($newPath, '/');
                $oldPath = ltrim($model->model_url, '/');

                // Kiểm tra xem file cũ có tồn tại trên MinIO không
                if (!Storage::disk('minio')->exists($oldPath)) {
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

                // Đổi tên file trên MinIO
                $moved = Storage::disk('minio')->move($oldPath, $minioNewPath);
                if (!$moved) {
                    return [
                        'success' => false,
                        'message' => 'Lỗi khi đổi tên file trên MinIO',
                    ];
                }

                $data['model_url'] = $newPath;
                $model->update($data);
            }

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
