<?php

declare(strict_types=1);

namespace App\Domains\Notification\Action;

use App\Domains\Notification\Model\Notification;
use App\Domains\Notification\Model\UserNotification;
use App\Domains\User\Model\User;
use App\Domains\User\Role\Model\Role;
use Illuminate\Support\Facades\Log;

class Create extends ActionAbstract
{
    protected array $data;

    public function handle(array $data, User $auth): Notification
    {
        $this->data = $data;
        $this->auth = $auth;
        return $this->createNotification();
    }

    protected function createNotification(): Notification
    {
        Log::info('Processing notification creation with data: ', $this->data);

        try {
            // Kiểm tra dữ liệu hợp lệ
            if (empty($this->data['title']) || empty($this->data['content'])) {
                throw new \Exception(__('notification-create.invalid-data'));
            }

            // Kiểm tra target_group (nếu có) có hợp lệ không
            if (!empty($this->data['target_group'])) {
                $roleExists = Role::where('name', $this->data['target_group'])
                    ->where('enterprise_id', $this->data['enterprise_id'] ?? null)
                    ->exists();
                if (!$roleExists) {
                    throw new \Exception(__('notification-create.invalid-target-group'));
                }
            }

            // Tạo thông báo
            $notification = Notification::create([
                'title' => $this->data['title'],
                'content' => $this->data['content'],
                'notification_type' => $this->data['notification_type'],
                'enterprise_id' => $this->data['enterprise_id'],
                'sender_id' => $this->auth->id,
                'target_group' => $this->data['target_group'] ?? null,
            ]);

            if (!$notification) {
                Log::error('Failed to create notification: ', $this->data);
                throw new \Exception(__('notification-create.failed'));
            }

            // Gán thông báo cho người nhận (dựa trên target_group hoặc tất cả user trong enterprise)
            $this->assignNotificationToUsers($notification);

            Log::info('Notification created successfully: ', $notification->toArray());
            return $notification;

        } catch (\Exception $e) {
            Log::error('Error creating notification: ', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    protected function assignNotificationToUsers(Notification $notification): void
    {
        $enterpriseId = $notification->enterprise_id;
        $targetGroup = $notification->target_group;

        // Nếu là thông báo hệ thống và không có target_group, gán cho tất cả user
        if ($notification->notification_type === 'system' && !$targetGroup) {
            $users = User::all();
        } elseif ($targetGroup) {
            // Gán cho user có vai trò khớp với target_group
            $users = User::whereHas('roles', function ($query) use ($targetGroup, $enterpriseId) {
                $query->where('name', $targetGroup);
                if ($enterpriseId) {
                    $query->where('enterprise_id', $enterpriseId);
                }
            })->get();
        } else {
            // Gán cho tất cả user trong enterprise
            $users = User::where('enterprise_id', $enterpriseId)->get();
        }

        // Tạo bản ghi trong user_notification
        foreach ($users as $user) {
            UserNotification::create([
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'read_at' => null,
            ]);
        }
    }
}