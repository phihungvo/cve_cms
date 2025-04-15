<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Media\Action;

use App\Domains\Campaign\Media\Model\Media;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class Delete
{
    public function handle($mediaUrl, $user): array
    {
        try {
            $media = Media::where('media_url', $mediaUrl)->first();

            if (!$media) {
                return [
                    'success' => false,
                    'message' => __('media-delete.delete-error-not-found'),
                ];
            }

            // Kiểm tra quyền (người không phải root chỉ xóa mềm media của enterprise mình)
            if (!$user->hasRole('root') && $media->enterprise_id !== $user->enterprise_id) {
                return [
                    'success' => false,
                    'message' => __('media-delete.no-permission'),
                ];
            }

            // Thực hiện soft delete
            $media->delete();

            return [
                'success' => true,
                'message' => __('media-delete.delete-success'),
            ];
        } catch (Exception $e) {
            Log::error('Error soft deleting media: ', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => __('media-delete.delete-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }

    // Action force delete (dành cho root)
    public function forceDelete($mediaId, $user): array
    {
        try {
            if (!$user->hasRole('root')) {
                return [
                    'success' => false,
                    'message' => __('media-delete.no-permission'),
                ];
            }

            $media = Media::withTrashed()->findOrFail($mediaId);

            // Xóa file từ MinIO
            $parsedUrl = parse_url($media->media_url, PHP_URL_PATH);
            $filePath = ltrim($parsedUrl, '/');
            $bucketName = config('filesystems.disks.minio.bucket');
            $filePath = str_replace($bucketName . '/', '', $filePath);

            if (Storage::disk('minio')->exists($filePath)) {
                Storage::disk('minio')->delete($filePath);
            }

            // Xóa hoàn toàn bản ghi từ database
            $media->forceDelete();

            return [
                'success' => true,
                'message' => __('media-delete.force-delete-success'),
            ];
        } catch (Exception $e) {
            Log::error('Error force deleting media: ', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => __('media-delete.force-delete-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }

    // Action restore (dành cho root)
    public function restore($mediaId, $user): array
    {
        try {
            if (!$user->hasRole('root')) {
                return [
                    'success' => false,
                    'message' => __('media-delete.no-permission'),
                ];
            }

            $media = Media::withTrashed()->findOrFail($mediaId);
            $media->restore();

            return [
                'success' => true,
                'message' => __('media-delete.restore-success'),
            ];
        } catch (Exception $e) {
            Log::error('Error restoring media: ', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => __('media-delete.restore-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }
}