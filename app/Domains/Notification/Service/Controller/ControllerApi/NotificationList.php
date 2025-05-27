<?php

declare(strict_types=1);

namespace App\Domains\Notification\Service\ControllerApi;

use App\Domains\Notification\Model\Notification;
use App\Domains\Notification\Model\UserNotification;
use App\Domains\User\Model\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class NotificationList
{
    protected $request;
    protected $auth;

    public function __construct(Request $request, ?User $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new(Request $request, ?User $auth): self
    {
        return new self($request, $auth);
    }

    public function data(): array
    {
        $user = $this->auth;
        if (!$user instanceof User) {
            throw new \Exception(__('notification-fetch.no-auth'));
        }

        $enterpriseId = $user->enterprise_id ?? null;
        $userRoles = $user->roles->pluck('name')->toArray();

        $query = Notification::query();

        // Root và Owner có thể thấy thông báo đã soft delete
        if ($user->hasRole('root') || $user->isOwner()) {
            $query->withTrashed();
        }

        $query->with([
            'sender',
            'enterprise',
            'userNotifications',
            'userNotifications.user'
        ]);

        // Lọc theo người dùng không phải Root
        if (!$user->hasRole('root')) {
            $query->where(function ($q) use ($enterpriseId, $user, $userRoles) {
                $q->whereNull('enterprise_id')
                    ->orWhere('enterprise_id', $enterpriseId)
                    ->orWhereHas('userNotifications', function ($subQ) use ($user) {
                        $subQ->where('user_id', $user->id);
                    })
                    ->orWhere(function ($subQ) use ($userRoles) {
                        $subQ->where('notification_type', 'system')
                            ->where(function ($q) use ($userRoles) {
                                $q->whereNull('target_group')
                                    ->orWhereIn('target_group', $userRoles);
                            });
                    });
            });
        }

        // Lọc theo tham số từ request
        if ($notificationType = $this->request->get('notification_type')) {
            $query->where('notification_type', $notificationType);
        }

        if ($enterpriseIdParam = $this->request->get('enterprise_id')) {
            $query->where('enterprise_id', $enterpriseIdParam);
        }

        if ($targetGroup = $this->request->get('target_group')) {
            $query->where('target_group', $targetGroup);
        }

        if ($readStatus = $this->request->get('read_status')) {
            $query->whereHas('userNotifications', function ($q) use ($readStatus, $user) {
                $q->where('user_id', $user->id);
                if ($readStatus === 'read') {
                    $q->whereNotNull('read_at');
                } elseif ($readStatus === 'unread') {
                    $q->whereNull('read_at');
                }
            });
        }

        if ($search = $this->request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                    ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        // Sắp xếp và phân trang
        $perPage = $this->request->get('per_page', 10);
        $query->orderBy('created_at', $this->request->get('sort', 'desc'));

        $notifications = $query->paginate($perPage);

        // Định dạng dữ liệu trả về
        return $this->formatNotifications($notifications);
    }

    protected function formatNotifications(LengthAwarePaginator $notifications): array
    {
        $user = $this->auth;

        $formatted = $notifications->getCollection()->map(function ($notification) use ($user) {
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
                $effectiveReadAt = now()->timestamp;
            }

            return [
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
                'read_at' => $effectiveReadAt ? date('Y-m-d H:i:s', $effectiveReadAt) : null,
                'read_count' => $readUsers,
                'total_count' => $totalUsers,
                'deleted_at' => $notification->deleted_at ? $notification->deleted_at->format('Y-m-d H:i:s') : null,
            ];
        })->toArray();

        return [
            'items' => $formatted,
            'pagination' => [
                'total' => $notifications->total(),
                'per_page' => $notifications->perPage(),
                'current_page' => $notifications->currentPage(),
                'last_page' => $notifications->lastPage(),
            ],
        ];
    }
}