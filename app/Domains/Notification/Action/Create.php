<?php

declare(strict_types=1);

namespace App\Domains\Notification\Action;

use App\Domains\Notification\Model\Notification;
use App\Domains\Notification\Model\UserNotification;
use App\Domains\User\Model\User;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Role\Model\Role;
use App\Domains\CoreApp\Action\ActionAbstract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;

class Create extends ActionAbstract
{
    protected array $data;
    protected ?Authenticatable $auth;
    protected ?Notification $row;

    public function handle(array $data, ?Authenticatable $auth): array
    {
        $this->data = $data;
        $this->auth = $auth;

        return $this->createNotification();
    }

    protected function createNotification(): array
    {
        Log::info('Processing notification creation with data: ', $this->data);

        try {
            // Kiểm tra quyền
            $this->checkAuthorization();

            // Tạo thông báo
            $this->row = Notification::create([
                'title' => $this->data['title'],
                'content' => $this->data['content'],
                'notification_type' => $this->data['notification_type'],
                'enterprise_id' => $this->data['enterprise_id'] ?? null,
                'sender_id' => $this->auth ? $this->auth->getAuthIdentifier() : null,
                'target_group' => $this->data['target_group'] ?? null,
                'created_at' => now(),
            ]);

            if (!$this->row) {
                throw new \Exception(__('notification-create.failed'));
            }

            // Gửi thông báo đến người dùng phù hợp
            $this->assignNotificationToUsers($this->row);

            Log::info('Notification created successfully: ', $this->row->toArray());

            return [
                'success' => true,
                'message' => __('notification-create.success'),
                'notification' => $this->row,
            ];
        } catch (\Exception $e) {
            Log::error('Error creating notification: ', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Kiểm tra quyền root hoặc owner
     *
     * @throws \Exception
     */
    protected function checkAuthorization(): void
    {
        if (!$this->auth instanceof User) {
            throw new \Exception(__('notification-create.no-permission'));
        }

        $user = $this->auth; // Gán vào biến tạm với kiểu User
        if (!$user->isRoot() && !$user->isOwner()) {
            throw new \Exception(__('notification-create.no-permission'));
        }

        if ($user->isOwner()) {
            if ($this->data['enterprise_id'] !== $user->enterprise_id && !is_null($this->data['enterprise_id'])) {
                throw new \Exception(__('notification-create.owner-enterprise-mismatch'));
            }
        }
    }

    protected function assignNotificationToUsers(Notification $notification): void
    {
        $usersQuery = User::query();

        // TH1: Người tạo là Root
        if ($this->auth->isRoot()) {
            // Nếu không chọn enterprise_id (system notification), hoặc enterprise_id là null
            if (is_null($notification->enterprise_id)) {
                // Gửi đến tất cả user thông thường (không phải Root)
                $usersQuery->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'root');
                });
            } else {
                // Nếu có chọn enterprise_id
                $usersQuery->where('enterprise_id', $notification->enterprise_id);

                // Trường hợp target_group và user_ids
                if ($notification->target_group === 'all' && empty($this->data['user_ids'])) {
                    // Gửi đến tất cả user thuộc enterprise
                    // Không cần thêm điều kiện vì đã giới hạn bởi enterprise_id
                } elseif (is_null($notification->target_group) || $notification->target_group === 'None') {
                    // Nếu target_group là null hoặc "None", gửi đến user_ids đã chọn
                    if (!empty($this->data['user_ids'])) {
                        $usersQuery->whereIn('id', $this->data['user_ids']);
                    }
                } elseif (empty($this->data['user_ids'])) {
                    // Nếu user_ids trống, gửi đến target_group đã chọn
                    if ($notification->target_group !== 'all') {
                        $usersQuery->whereHas('roles', function ($q) use ($notification) {
                            $q->where('name', $notification->target_group);
                        });
                    }
                } else {
                    // Nếu cả target_group và user_ids đều được chọn
                    $usersQuery->where(function ($q) use ($notification) {
                        if ($notification->target_group !== 'all') {
                            $q->whereHas('roles', function ($subQ) use ($notification) {
                                $subQ->where('name', $notification->target_group);
                            });
                        }
                        $q->orWhereIn('id', $this->data['user_ids']);
                    });
                }
            }
        }

        // TH2: Người tạo là Owner
        if ($this->auth->isOwner()) {
            $usersQuery->where('enterprise_id', $notification->enterprise_id);

            // Owner không gửi đến user là Root hoặc Owner
            $usersQuery->whereDoesntHave('roles', function ($q) {
                $q->whereIn('name', ['root', 'owner']);
            });

            // Trường hợp target_group và user_ids
            if ($notification->target_group === 'all' && empty($this->data['user_ids'])) {
                // Gửi đến tất cả user thông thường thuộc enterprise
                // Đã được giới hạn bởi whereDoesntHave và enterprise_id
            } elseif (is_null($notification->target_group) || $notification->target_group === 'None') {
                // Nếu target_group là null hoặc "None", gửi đến user_ids đã chọn
                if (!empty($this->data['user_ids'])) {
                    $usersQuery->whereIn('id', $this->data['user_ids']);
                }
            } elseif (empty($this->data['user_ids'])) {
                // Nếu user_ids trống, gửi đến target_group đã chọn
                if ($notification->target_group !== 'all') {
                    $usersQuery->whereHas('roles', function ($q) use ($notification) {
                        $q->where('name', $notification->target_group);
                    });
                }
            } else {
                // Nếu cả target_group và user_ids đều được chọn
                $usersQuery->where(function ($q) use ($notification) {
                    if ($notification->target_group !== 'all') {
                        $q->whereHas('roles', function ($subQ) use ($notification) {
                            $subQ->where('name', $notification->target_group);
                        });
                    }
                    $q->orWhereIn('id', $this->data['user_ids']);
                });
            }
        }

        $users = $usersQuery->get();

        foreach ($users as $user) {
            $this->createUserNotification($user, $notification);
        }
    }

    protected function createUserNotification(User $user, Notification $notification): void
    {
        UserNotification::create([
            'user_id' => $user->id,
            'notification_id' => $notification->id,
            'read_at' => null,
        ]);
    }
}