<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controller;

use App\Domains\Notification\Action\Delete;
use App\Domains\Notification\Service\Controller\Index as ControllerService;
use App\Domains\Notification\Model\Notification;
use App\Domains\Notification\Model\UserNotification;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class Index extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('notification-index.meta-title'));

        return $this->page('notification.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    public function destroy(): RedirectResponse
    {
        $notificationId = $this->request->input('notification_id');
        if (!is_numeric($notificationId)) {
            $this->sessionMessage('error', __('notification-delete.invalid-id'));
            return redirect()->route('notification.index');
        }
        $notificationId = (int) $notificationId;

        // Kiểm tra quyền xóa
        $query = Notification::query()->newQuery();
        $query->filterByPermission('access-notification-delete')
            ->where('id', $notificationId);
        if (!$this->auth->hasRole('root') && !$this->auth->isOwner() && !$query->exists()) {
            $this->sessionMessage('error', __('notification-delete.no-permission'));
            return redirect()->route('notification.index');
        }

        $action = new Delete();
        $result = $action->handle($notificationId, $this->auth);

        return $this->redirectResult($result, 'notification.index');
    }

    public function restore($id): RedirectResponse
    {
        try {
            // Kiểm tra quyền khôi phục
            $query = Notification::query()->newQuery();
            $query->filterByPermission('access-notification-restore')
                ->where('id', $id);
            if (!$this->auth->hasRole('root') && !$this->auth->isOwner() && !$query->exists()) {
                $this->sessionMessage('error', __('notification-restore.no-permission'));
                return redirect()->route('notification.index');
            }

            $action = new Delete();
            $result = $action->restore((int) $id, $this->auth);

            return $this->redirectResult($result, 'notification.index');
        } catch (\Exception $e) {
            Log::error('Restore notification failed', [
                'notification_id' => $id,
                'error' => $e->getMessage(),
                'stack' => $e->getTraceAsString(),
            ]);
            $this->sessionMessage('error', __('notification-restore.failed') . ': ' . $e->getMessage());
            return redirect()->route('notification.index');
        }
    }

    public function forceDelete($id): RedirectResponse
    {
        // Kiểm tra quyền xóa vĩnh viễn
        $query = Notification::query()->newQuery();
        $query->filterByPermission('access-notification-force-delete')
            ->where('id', $id);
        if (!$this->auth->hasRole('root') && !$this->auth->isOwner() && !$query->exists()) {
            $this->sessionMessage('error', __('notification-delete.no-permission'));
            return redirect()->route('notification.index');
        }

        $action = new Delete();
        $result = $action->forceDelete((int) $id, $this->auth);

        return $this->redirectResult($result, 'notification.index');
    }

    public function markAsRead($id): RedirectResponse
    {
        try {
            $user = $this->auth;

            if ($user->hasRole('root')) {
                $this->sessionMessage('error', __('notification-read.no-permission-root'));
                return redirect()->route('notification.index');
            }

            $userNotification = UserNotification::where('notification_id', $id)
                ->where('user_id', $user->id)
                ->first();

            if (!$userNotification) {
                $this->sessionMessage('error', __('notification-read.no-permission'));
                return redirect()->route('notification.index');
            }

            if ($userNotification->read_at) {
                $this->sessionMessage('info', __('notification-read.already-read'));
                return redirect()->route('notification.index');
            }

            $userNotification->update(['read_at' => now()]);
            Cache::forget("unread_notifications_{$user->id}");

            $this->sessionMessage('success', __('notification-read.success'));
            return redirect()->route('notification.index');
        } catch (\Exception $e) {
            $this->sessionMessage('error', __('notification-read.failed') . ': ' . $e->getMessage());
            return redirect()->route('notification.index');
        }
    }

    public function show($id): Response|RedirectResponse
    {
        try {
            $user = $this->auth;
            $notificationQuery = Notification::query()->newQuery();
            $notificationQuery->filterByPermission('access-notification-list')
                ->where('id', $id);
            $notification = $notificationQuery->withTrashed()
                ->with([
                    'sender',
                    'enterprise',
                    'userNotifications',
                    'userNotifications.user'
                ])
                ->first();

            if (!$notification) {
                $this->sessionMessage('error', __('notification-show.not-found'));
                return redirect()->route('notification.index');
            }

            // Kiểm tra quyền xem thông báo
            $hasAccess = $user->hasRole('root') || (
                $notification->userNotifications->where('user_id', $user->id)->isNotEmpty() ||
                ($notification->notification_type === 'system' && (
                    !$notification->target_group || in_array($notification->target_group, $user->roles->pluck('name')->toArray())
                )) ||
                ($notification->notification_type === 'enterprise' && $notification->enterprise_id === $user->enterprise_id) ||
                $user->hasPermission('access-notification-list')
            );

            if (!$hasAccess) {
                $this->sessionMessage('error', __('notification-show.no-permission'));
                return redirect()->route('notification.index');
            }

            // Tự động đánh dấu đã đọc nếu chưa đọc và không phải Root
            $userNotification = $notification->userNotifications->firstWhere('user_id', $user->id);
            if ($userNotification && !$userNotification->read_at && !$user->hasRole('root')) {
                $userNotification->update(['read_at' => now()]);
                Cache::forget("unread_notifications_{$user->id}");
            }

            $nonRootUsers = $notification->userNotifications->filter(function ($userNotification) {
                return !$userNotification->user || !$userNotification->user->hasRole('root');
            });

            $totalUsers = $nonRootUsers->count();
            $readUsers = $nonRootUsers->whereNotNull('read_at')->count();

            $data = [
                'id' => $notification->id,
                'title' => $notification->title,
                'content' => $notification->content,
                'notification_type' => $notification->notification_type,
                'enterprise_id' => $notification->enterprise_id,
                'enterprise_name' => $notification->enterprise ? $notification->enterprise->name : 'N/A',
                'sender_id' => $notification->sender_id,
                'sender_name' => $notification->sender ? $notification->sender->name : 'N/A',
                'target_group' => $notification->target_group ?? 'All',
                'created_at' => $notification->created_at->format('Y-m-d H:i:s'),
                'read_at' => $userNotification ? $userNotification->read_at?->format('Y-m-d H:i:s') : null,
                'read_count' => $readUsers,
                'total_count' => $totalUsers,
                'deleted_at' => $notification->deleted_at ? $notification->deleted_at->format('Y-m-d H:i:s') : null,
            ];

            $this->meta('title', __('notification-show.title'));

            return $this->page('notification.show', ['notification' => $data]);
        } catch (\Exception $e) {
            Log::error('Error showing notification: ', ['error' => $e->getMessage()]);
            $this->sessionMessage('error', __('notification-show.failed') . ': ' . $e->getMessage());
            return redirect()->route('notification.index');
        }
    }

    public function deviceStatus($id): \Illuminate\Http\JsonResponse
    {
        try {
            $user = $this->auth;

            // Kiểm tra quyền truy cập
            $query = Notification::query()->newQuery();
            $query->filterByPermission('access-notification-list')
                ->where('id', $id);
            if (!$user->hasRole('root') && $user->id !== Notification::findOrFail($id)->sender_id && !$query->exists()) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('notification-show.no-permission'),
                ], 403);
            }

            $notification = Notification::with(['devices'])->findOrFail($id);
            $displays = \App\Domains\Display\Model\Display::where('notification_id', $id)->get();

            $totalSent = $displays->count();
            $totalRead = $displays->where('notification_published', 1)->count();

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_sent' => $totalSent,
                    'total_read' => $totalRead,
                ],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching device status: ', [
                'notification_id' => $id,
                'error' => $e->getMessage(),
            ]);
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to load device stats') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    public function unreadCount(): \Illuminate\Http\JsonResponse
    {
        try {
            $userId = $this->auth->id;
            $unreadCount = Cache::remember("unread_notifications_{$userId}", 60, function () use ($userId) {
                return UserNotification::where('user_id', $userId)
                    ->whereNull('read_at')
                    ->count();
            });

            return response()->json([
                'status' => 'success',
                'data' => ['unread_count' => $unreadCount],
            ]);
        } catch (\Exception $e) {
            Log::error('Error fetching unread notification count: ', ['error' => $e->getMessage()]);
            return response()->json([
                'status' => 'error',
                'message' => __('Failed to fetch unread count') . ': ' . $e->getMessage(),
            ], 500);
        }
    }

    protected function redirectResult(array $result, string $route): RedirectResponse
    {
        if ($result['success']) {
            $this->sessionMessage('success', $result['message']);
        } else {
            $this->sessionMessage('error', $result['message']);
        }
        return redirect()->route($route);
    }
}