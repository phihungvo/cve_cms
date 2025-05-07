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
        $action = new Delete();
        $result = $action->handle($notificationId, $this->auth);

        return $this->redirectResult($result, 'notification.index');
    }

    public function restore($id): RedirectResponse
    {
        $action = new Delete();
        $result = $action->restore($id, $this->auth);

        return $this->redirectResult($result, 'notification.index');
    }

    public function forceDelete($id): RedirectResponse
    {
        $action = new Delete();
        $result = $action->forceDelete($id, $this->auth);

        return $this->redirectResult($result, 'notification.index');
    }

    public function markAsRead($id): RedirectResponse
    {
        try {
            $userId = $this->auth->id;
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
            $notification = Notification::query()
                ->with([
                    'sender',
                    'enterprise',
                    'userNotifications' => function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    }
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

            // Tự động đánh dấu là đã đọc nếu chưa đọc
            $userNotification = $notification->userNotifications->first();
            if ($userNotification && !$userNotification->read_at) {
                $userNotification->update(['read_at' => now()]);
            }

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
            ];

            $this->meta('title', __('notification-show.title'));

            return $this->page('notification.show', ['notification' => $data]);
        } catch (\Exception $e) {
            Log::error('Error showing notification: ', ['error' => $e->getMessage()]);
            $this->sessionMessage('error', __('notification-show.failed') . ': ' . $e->getMessage());
            return redirect()->route('notification.index');
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