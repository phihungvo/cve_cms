<?php

declare(strict_types=1);

namespace App\Domains\Notification\Action;

use App\Domains\Notification\Model\Notification;
use App\Domains\User\Model\User;
use Exception;
use Illuminate\Support\Facades\Log;

class Delete
{
    public function handle(int $notificationId, User $user): array
    {
        try {
            $notification = Notification::find($notificationId);

            if (!$notification) {
                return [
                    'success' => false,
                    'message' => __('notification-delete.delete-error-not-found'),
                ];
            }

            // Kiểm tra quyền
            if (!$user->hasRole('root') && ($notification->sender_id !== $user->id || $notification->enterprise_id !== $user->enterprise_id)) {
                return [
                    'success' => false,
                    'message' => __('notification-delete.no-permission'),
                ];
            }

            // Thực hiện soft delete
            $notification->delete();

            Log::info('Notification soft deleted successfully: ', ['id' => $notification->id]);

            return [
                'success' => true,
                'message' => __('notification-delete.delete-success'),
            ];
        } catch (Exception $e) {
            Log::error('Error soft deleting notification: ', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => __('notification-delete.delete-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }

    public function forceDelete(int $notificationId, User $user): array
    {
        try {
            if (!$user->hasRole('root')) {
                return [
                    'success' => false,
                    'message' => __('notification-delete.no-permission'),
                ];
            }

            $notification = Notification::withTrashed()->findOrFail($notificationId);

            // Xóa vĩnh viễn bản ghi
            $notification->forceDelete();

            Log::info('Notification force deleted successfully: ', ['id' => $notificationId]);

            return [
                'success' => true,
                'message' => __('notification-delete.force-delete-success'),
            ];
        } catch (Exception $e) {
            Log::error('Error force deleting notification: ', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => __('notification-delete.force-delete-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }

    public function restore(int $notificationId, User $user): array
    {
        try {
            if (!$user->hasRole('root')) {
                return [
                    'success' => false,
                    'message' => __('notification-delete.no-permission'),
                ];
            }

            $notification = Notification::withTrashed()->findOrFail($notificationId);
            $notification->restore();

            Log::info('Notification restored successfully: ', ['id' => $notificationId]);

            return [
                'success' => true,
                'message' => __('notification-delete.restore-success'),
            ];
        } catch (Exception $e) {
            Log::error('Error restoring notification: ', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => __('notification-delete.restore-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }
}