<?php

declare(strict_types=1);

namespace App\Domains\Notification\Action;

use App\Domains\Notification\Model\Notification;
use App\Domains\Notification\Model\UserNotification;
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

            // Kiểm tra quyền: chỉ Root hoặc Owner của Enterprise có quyền soft delete
            if (!$user->hasRole('root') && !$user->isOwner()) {
                return [
                    'success' => false,
                    'message' => __('notification-delete.no-permission'),
                ];
            }

            // Nếu là Owner, kiểm tra Enterprise
            if ($user->isOwner() && $notification->enterprise_id && $notification->enterprise_id !== $user->enterprise_id) {
                return [
                    'success' => false,
                    'message' => __('notification-delete.no-permission-enterprise'),
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
            Log::error('Error soft deleting notification: ', ['error' => $e->getMessage(), 'notificationId' => $notificationId]);
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
            if ($notificationId <= 0) {
                Log::error('Invalid notification ID provided for force delete', ['notificationId' => $notificationId, 'user_id' => $user->id]);
                return [
                    'success' => false,
                    'message' => __('notification-delete.invalid-id'),
                ];
            }

            if (!$user->hasRole('root') && !$user->isOwner()) {
                return [
                    'success' => false,
                    'message' => __('notification-delete.no-permission'),
                ];
            }

            $notification = Notification::withTrashed()->findOrFail($notificationId);

            if (!$notification->trashed()) {
                return [
                    'success' => false,
                    'message' => __('notification-delete.not-soft-deleted'),
                ];
            }

            if ($user->isOwner() && $notification->enterprise_id && $notification->enterprise_id !== $user->enterprise_id) {
                return [
                    'success' => false,
                    'message' => __('notification-delete.no-permission-enterprise'),
                ];
            }

            UserNotification::where('notification_id', $notificationId)->delete();
            $notification->forceDelete();

            Log::info('Notification force deleted successfully: ', ['id' => $notificationId, 'user_id' => $user->id]);

            return [
                'success' => true,
                'message' => __('notification-delete.force-delete-success'),
            ];
        } catch (Exception $e) {
            Log::error('Error force deleting notification: ', [
                'error' => $e->getMessage(),
                'notificationId' => $notificationId,
                'user_id' => $user->id,
                'request_ip' => request()->ip(),
            ]);
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
            if ($notificationId <= 0) {
                Log::error('Invalid notification ID provided for restore', ['notificationId' => $notificationId, 'user_id' => $user->id]);
                return [
                    'success' => false,
                    'message' => __('notification-delete.invalid-id'),
                ];
            }

            if (!$user->hasRole('root') && !$user->isOwner()) {
                return [
                    'success' => false,
                    'message' => __('notification-delete.no-permission'),
                ];
            }

            $notification = Notification::withTrashed()->findOrFail($notificationId);

            if (!$notification->trashed()) {
                return [
                    'success' => false,
                    'message' => __('notification-delete.not-soft-deleted'),
                ];
            }

            if ($user->isOwner() && $notification->enterprise_id && $notification->enterprise_id !== $user->enterprise_id) {
                return [
                    'success' => false,
                    'message' => __('notification-delete.no-permission-enterprise'),
                ];
            }

            $notification->restore();

            Log::info('Notification restored successfully: ', ['id' => $notificationId, 'user_id' => $user->id]);

            return [
                'success' => true,
                'message' => __('notification-delete.restore-success'),
            ];
        } catch (Exception $e) {
            Log::error('Error restoring notification: ', [
                'error' => $e->getMessage(),
                'notificationId' => $notificationId,
                'user_id' => $user->id,
                'request_ip' => request()->ip(),
            ]);
            return [
                'success' => false,
                'message' => __('notification-delete.restore-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }
}