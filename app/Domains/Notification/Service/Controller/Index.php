<?php

declare(strict_types=1);

namespace App\Domains\Notification\Service\Controller;

use App\Domains\Notification\Model\Notification;
use App\Domains\Notification\Model\UserNotification;
use App\Domains\User\Model\User;
use App\Domains\Notification\Model\Builder\NotificationBuilder;

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

        // Khởi tạo query với NotificationBuilder
        $query = Notification::query();

        // Áp dụng logic cho Root và Owner
        if ($user->hasRole('root')) {
            $query->roleRoot()->withTrashed();

        } else {
            // Áp dụng filter cho user không phải Root
            $query->where(function ($q) use ($user, $userRoles, $enterpriseId) {
                $q->whereHas('userNotifications', function ($subQ) use ($user) {
                    $subQ->where('user_id', $user->id);
                })
                    ->orWhere(function ($subQ) use ($userRoles) {
                        $subQ->where('notification_type', 'system')
                            ->where(function ($q) use ($userRoles) {
                                $q->whereNull('target_group')
                                    ->orWhereIn('target_group', $userRoles);
                            });
                    })
                    ->orWhere(function ($subQ) use ($enterpriseId, $userRoles) {
                        $subQ->where('notification_type', 'enterprise')
                            ->where('enterprise_id', $enterpriseId)
                            ->where(function ($q) use ($userRoles) {
                                $q->whereNull('target_group')
                                    ->orWhereIn('target_group', $userRoles)
                                    ->orWhere('target_group', 'all');
                            });
                    });
            });
        }

        // Áp dụng filter theo quyền
        $query->filterByPermission('access-notification-list');

        // Load các quan hệ
        $query->with([
            'sender',
            'userNotifications',
            'userNotifications.user'
        ]);

        // Xử lý tìm kiếm
        if ($search = $this->request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }


        // Sắp xếp theo created_at
        $query->orderBy('created_at', 'desc');
        $notifications = $query->get();

        // Ánh xạ dữ liệu
        return $notifications->map(function ($notification) use ($user) {
            $userNotification = $notification->userNotifications->firstWhere('user_id', $user->id);

            // Lấy danh sách user không phải Root
            $nonRootUsers = $notification->userNotifications->filter(function ($userNotification) {
                return !$userNotification->user || !$userNotification->user->hasRole('root');
            });

            // Đếm số lượng user không phải Root đã đọc và tổng số user không phải Root nhận thông báo
            $totalUsers = $nonRootUsers->count();
            $readUsers = $nonRootUsers->whereNotNull('read_at')->count();

            // Cập nhật trạng thái read_at hiệu quả
            $effectiveReadAt = $userNotification ? $userNotification->read_at?->timestamp : null;
            if ($totalUsers > 0 && $readUsers === $totalUsers) {
                $effectiveReadAt = now()->timestamp; // Đặt trạng thái là đã đọc nếu tất cả user không phải Root đã đọc
            }

            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'content' => $notification->content,
                'notification_type' => $notification->notification_type,
                'enterprise_id' => $notification->enterprise_id,
                'sender_id' => $notification->sender_id,
                'sender_name' => $notification->sender ? $notification->sender->name : null,
                'target_group' => $notification->target_group ?? 'All',
                'created_at' => $notification->created_at->timestamp,
                'read_at' => $effectiveReadAt,
                'read_count' => $readUsers,
                'total_count' => $totalUsers,
                'deleted_at' => $notification->deleted_at ? $notification->deleted_at->format('Y-m-d H:i:s') : null,
            ];
        })->all();
    }
}