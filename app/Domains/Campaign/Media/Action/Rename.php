<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Media\Action;

use App\Domains\Campaign\Media\Model\Media;
use Exception;
use Illuminate\Support\Facades\Log;

class Rename
{
    public function handle($mediaId, $newName, $user): array
    {
        try {
            $media = Media::find($mediaId);

            if (!$media) {
                return [
                    'success' => false,
                    'message' => __('media-rename.rename-error-not-found'),
                ];
            }

            // Kiểm tra quyền: Root hoặc người dùng thuộc enterprise của media
            if (!$user->hasRole('root') && $media->enterprise_id !== $user->enterprise_id) {
                return [
                    'success' => false,
                    'message' => __('media-rename.no-permission'),
                ];
            }

            // Chỉ cập nhật tên trong database, không thay đổi file trên MinIO
            $media->update([
                'name' => $newName,
            ]);

            return [
                'success' => true,
                'message' => __('media-rename.rename-success'),
            ];
        } catch (Exception $e) {
            Log::error('Error renaming media: ', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => __('media-rename.rename-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }
}