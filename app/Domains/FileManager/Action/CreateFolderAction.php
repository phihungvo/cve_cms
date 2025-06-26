<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Action;

use App\Domains\FileManager\Model\FileManager as Model;
use Illuminate\Support\Facades\Storage;

class CreateFolderAction
{
    /**
     * Handle the folder creation logic.
     *
     * @param array $data
     *
     * @throws \Exception
     *
     * @return Model
     */
    public function handle(array $data): Model
    {
        try {
            $folderName = trim($data['folder_name']);
            $enterpriseId = $data['enterprise_id'] ?? null;
            $parentPath = $data['parent_path'] ?? '';

            // Chuẩn hóa basePath để luôn bắt đầu bằng enterprise_id nếu có
            $basePath = $parentPath ?: ($enterpriseId ? $enterpriseId : '');
//            $basePath = $enterpriseId ? "{$enterpriseId}/".ltrim($parentPath, '/') : ltrim($parentPath, '/');
            $path = $basePath ? "{$basePath}/{$folderName}" : $folderName;
            $fullPath = '/'.trim($path, '/');

            // Check if folder already exists in MinIO or database
            if (Storage::disk('minio')->exists($path) || Model::where('model_url', $fullPath)->exists()) {
                throw new \Exception('Thư mục đã tồn tại');
            }

            // Create directory in MinIO
            Storage::disk('minio')->makeDirectory($path);

            // Find parent folder ID
            $parentId = $parentPath ? Model::where('model_url', '/'.trim($basePath, '/'))->first()?->id : null;

            // Create folder record in database
            return Model::create([
                'name' => $folderName,
                'file_name' => $folderName,
                'model_url' => $fullPath,
                'size' => 0,
                'type' => 'folder',
                'enterprise_id' => $enterpriseId,
                'parent_id' => $parentId,
                'is_folder' => true,
            ]);
        } catch (\Exception $e) {
            throw new \Exception('Lỗi khi tạo thư mục: '.$e->getMessage());
        }
    }
}
