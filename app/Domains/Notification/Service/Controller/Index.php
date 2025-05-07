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

        $query = Notification::query();
        if ($user->hasRole('root') || $user->isOwner()) {
            $query->withTrashed(); // Root và Owner thấy cả thông báo đã soft delete
        }

        $query->with([
            'sender',
            'userNotifications',
            'userNotifications.user'
        ]);

        if (!$user->hasRole('root')) {
            // Owner và user thường chỉ thấy thông báo của enterprise của họ
            if ($user->isOwner() || !$user->isOwner()) {
                $query->where(function ($q) use ($enterpriseId) {
                    $q->whereNull('enterprise_id')
                        ->orWhere('enterprise_id', $enterpriseId);
                });
            }
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
                'target_group' => $notification->target_group,
                'created_at' => $notification->created_at->timestamp,
                'read_at' => $effectiveReadAt,
                'read_count' => $readUsers,
                'total_count' => $totalUsers,
                'deleted_at' => $notification->deleted_at ? $notification->deleted_at->format('Y-m-d H:i:s') : null,
            ];
        })->all();
    }
}