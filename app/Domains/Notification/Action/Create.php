<?php

declare(strict_types=1);

namespace App\Domains\Notification\Action;

use App\Domains\Notification\Model\Notification;
use App\Domains\Notification\Model\UserNotification;
use App\Domains\User\Model\User;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Role\Model\Role;
use App\Domains\CoreApp\Action\ActionAbstract;
use Illuminate\Support\Facades\Log;

class Create extends ActionAbstract
{
    protected array $data;
    protected $auth;

    public function handle(array $data, $auth): array
    {
        $this->data = $data;
        $this->auth = $auth;

        return $this->createNotification();
    }

    protected function createNotification(): array
    {
        Log::info('Processing notification creation with data: ', $this->data);

        try {
            // Kiểm tra quyền root hoặc owner
            if (!$this->auth->isRoot() && !$this->auth->isOwner()) {
                throw new \Exception(__('notification-create.no-permission'));
            }

            // Nếu là owner, giới hạn enterprise_id
            if ($this->auth->isOwner()) {
                if ($this->data['enterprise_id'] !== $this->auth->enterprise_id) {
                    throw new \Exception(__('notification-create.owner-enterprise-mismatch'));
                }
            }

            // Tạo thông báo
            $notification = Notification::create([
                'title' => $this->data['title'],
                'content' => $this->data['content'],
                'notification_type' => $this->data['notification_type'],
                'enterprise_id' => $this->data['enterprise_id'] ?? null,
                'sender_id' => $this->auth->id,
                'target_group' => $this->data['target_group'] ?? null,
                'created_at' => now(),
            ]);

            if (!$notification) {
                throw new \Exception(__('notification-create.failed'));
            }

            // Gửi thông báo đến người dùng phù hợp
            $this->assignNotificationToUsers($notification);

            Log::info('Notification created successfully: ', $notification->toArray());

            return [
                'success' => true,
                'message' => __('notification-create.success'),
                'notification' => $notification,
            ];
        } catch (\Exception $e) {
            Log::error('Error creating notification: ', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    protected function assignNotificationToUsers(Notification $notification): void
    {
        $usersQuery = User::query();

        // Nếu có user_ids được chỉ định, ưu tiên gửi đến các user này
        if (!empty($this->data['user_ids'])) {
            $usersQuery->whereIn('id', $this->data['user_ids']);
        } else {
            // Nếu không có user_ids, gửi theo enterprise_id và target_group
            if ($notification->notification_type === 'enterprise' && $notification->enterprise_id) {
                $usersQuery->where('enterprise_id', $notification->enterprise_id);
            }

            if ($notification->target_group) {
                $usersQuery->whereHas('roles', function ($q) use ($notification) {
                    $q->where('name', $notification->target_group);
                });
            }
        }

        // Nếu là owner, giới hạn user trong enterprise của họ
        if ($this->auth->isOwner()) {
            $usersQuery->where('enterprise_id', $this->auth->enterprise_id);
        }

        $users = $usersQuery->get();

        foreach ($users as $user) {
            UserNotification::create([
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'read_at' => null,
            ]);
        }
    }
}