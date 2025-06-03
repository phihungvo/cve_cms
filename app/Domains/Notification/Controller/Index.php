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
        $notificationId = (int) $notificationId; // Ép kiểu thành int
        $action = new Delete();
        $result = $action->handle($notificationId, $this->auth);

        return $this->redirectResult($result, 'notification.index');
    }

    public function restore($id): RedirectResponse
    {
        try {
            $user = $this->auth;

            if (!$user->hasRole('root') && !$user->isOwner()) {
                $this->sessionMessage('error', __('notification-restore.no-permission-owner'));
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
        $action = new Delete();
        $result = $action->forceDelete((int) $id, $this->auth);

        return $this->redirectResult($result, 'notification.index');
    }

    public function markAsRead($id): RedirectResponse
    {
        try {
            $user = $this->auth;

            // Root không được phép đánh dấu đã đọc
            if ($user->hasRole('root')) {
                $this->sessionMessage('error', __('notification-read.no-permission-root'));
                return redirect()->route('notification.index');
            }

            $userId = $user->id;
            $userNotification = UserNotification::where('notification_id', $id)
                ->where('user_id', $userId)
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
            $notification = Notification::withTrashed() // Hiển thị cả thông báo đã soft delete cho Root
                ->with([
                    'sender',
                    'enterprise',
                    'userNotifications',
                    'userNotifications.user'
                ])
                ->where('id', $id)
                ->first();

            if (!$notification) {
                $this->sessionMessage('error', __('notification-show.not-found'));
                return redirect()->route('notification.index');
            }

            // Kiểm tra quyền truy cập
            $hasAccess = $user->hasRole('root') || (
                $notification->userNotifications->where('user_id', $user->id)->isNotEmpty() ||
                ($notification->notification_type === 'system' && (
                    !$notification->target_group || in_array($notification->target_group, $user->roles->pluck('name')->toArray())
                )) ||
                ($notification->notification_type === 'enterprise' && $notification->enterprise_id === $user->enterprise_id)
            );

            if (!$hasAccess) {
                $this->sessionMessage('error', __('notification-show.no-permission'));
                return redirect()->route('notification.index');
            }

            // Tự động đánh dấu đã đọc nếu chưa đọc và không phải Root
            $userNotification = $notification->userNotifications->firstWhere('user_id', $user->id);
            if ($userNotification && !$userNotification->read_at && !$user->hasRole('root')) {
                $userNotification->update(['read_at' => now()]);
            }

            // Lấy danh sách user không phải Root
            $nonRootUsers = $notification->userNotifications->filter(function ($userNotification) {
                return !$userNotification->user || !$userNotification->user->hasRole('root');
            });

            // Đếm số lượng user không phải Root đã đọc và tổng số user không phải Root nhận thông báo
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

            // Kiểm tra quyền truy cập (chỉ root hoặc người gửi thông báo)
            if (!$user->hasRole('root') && $user->id !== Notification::findOrFail($id)->sender_id) {
                return response()->json([
                    'status' => 'error',
                    'message' => __('notification-show.no-permission'),
                ], 403);
            }

            // Lấy thông báo
            $notification = Notification::with(['devices'])->findOrFail($id);

            // Lấy danh sách thiết bị liên quan từ bảng display
            $displays = \App\Domains\Display\Model\Display::where('notification_id', $id)->get();

            // Đếm số lượng thiết bị đã gửi và đã đọc
            $totalSent = $displays->count();
            $totalRead = $displays->where('notification_published', 1)->count(); // Giả sử notification_published = 1 là đã đọc

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
            $unreadCount = UserNotification::where('user_id', $userId)
                ->whereNull('read_at')
                ->count();

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