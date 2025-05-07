<?php

declare(strict_types=1);

namespace App\Domains\Notification\Service\Controller;

use App\Domains\Notification\Model\Notification;
use App\Domains\Notification\Model\UserNotification;
use App\Domains\User\Model\User;

class Index
{
    protected $request;
    protected $auth;

    public function __construct($request, $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new($request, $auth): self
    {
        return new self($request, $auth);
    }

    public function data(): array
    {
        return [
            'notifications' => $this->getNotifications(),
        ];
    }

    protected function getNotifications()
    {
        $user = $this->auth;
        $enterpriseId = $user->enterprise_id ?? null;
        $userRoles = $user->roles->pluck('name')->toArray();

        $query = Notification::query()
            ->with([
                'sender',
                'userNotifications' => function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                }
            ]);

        if ($user->hasRole('root')) {
            // Root thấy tất cả thông báo
        } else {
            $query->where(function ($q) use ($enterpriseId, $user, $userRoles) {
                $q->where('notification_type', 'system')
                    ->where(function ($subQ) use ($userRoles) {
                        $subQ->whereNull('target_group')
                            ->orWhereIn('target_group', $userRoles);
                    })
                    ->orWhere(function ($subQ) use ($enterpriseId, $user, $userRoles) {
                        $subQ->where('notification_type', 'enterprise')
                            ->where('enterprise_id', $enterpriseId)
                            ->where(function ($innerQ) use ($user, $userRoles) {
                                $innerQ->whereIn('target_group', $userRoles)
                                    ->orWhereHas('userNotifications', function ($q) use ($user) {
                                        $q->where('user_id', $user->id);
                                    });
                            });
                    })
                    // Thêm điều kiện cho thông báo từ root gửi trực tiếp
                    ->orWhereHas('userNotifications', function ($q) use ($user) {
                        $q->where('user_id', $user->id);
                    });
            });
        }

        if ($search = $this->request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        $query->orderBy('created_at', 'desc');
        $notifications = $query->get();

        return $notifications->map(function ($notification) use ($user) {
            $userNotification = $notification->userNotifications->firstWhere('user_id', $user->id);
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'content' => $notification->content,
                'notification_type' => $notification->notification_type,
                'enterprise_id' => $notification->enterprise_id,
                'sender_id' => $notification->sender_id,
                'sender_name' => $notification->sender ? $notification->sender->name : null,
                'target_group' => $notification->target_group,
                'created_at' => $notification->created_at->timestamp,
                'read_at' => $userNotification ? $userNotification->read_at?->timestamp : null,
            ];
        })->all();
    }
}