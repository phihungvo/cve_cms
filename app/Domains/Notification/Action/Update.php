<?php

declare(strict_types=1);

namespace App\Domains\Notification\Action;

use App\Domains\Notification\Model\Notification;
use App\Domains\User\Model\User;
use App\Domains\User\Role\Model\Role;
use App\Domains\CoreApp\Action\ActionAbstract;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Log;

class Update extends ActionAbstract
{
    protected array $data;
    protected ?Notification $row;
    protected ?Authenticatable $auth;

    public function handle(Notification $notification, array $data, ?Authenticatable $auth): Notification
    {
        $this->row = $notification;
        $this->data = $data;
        $this->auth = $auth;

        return $this->updateNotification();
    }

    protected function updateNotification(): Notification
    {
        Log::info('Updating notification with data: ', $this->data);

        try {
            // Kiểm tra $row không null
            if (!$this->row instanceof Notification) {
                throw new \Exception(__('notification-update.invalid-notification'));
            }

            // Kiểm tra quyền
            $this->checkAuthorization();

            // Kiểm tra target_group (nếu có) có hợp lệ không
            if (!empty($this->data['target_group']) && $this->data['target_group'] !== 'all') {
                $roleExists = Role::where('name', $this->data['target_group'])
                    ->where('enterprise_id', $this->row->enterprise_id ?? null)
                    ->exists();
                if (!$roleExists) {
                    throw new \Exception(__('notification-update.invalid-target-group'));
                }
            }

            // Dữ liệu để cập nhật
            $dataToUpdate = [
                'title' => $this->data['title'],
                'content' => $this->data['content'],
                'notification_type' => $this->data['notification_type'],
                'enterprise_id' => $this->data['enterprise_id'] ?? null,
                'target_group' => $this->data['target_group'] ?? null,
            ];

            // Cập nhật thông báo
            $updated = $this->row->update($dataToUpdate);

            if ($updated) {
                Log::info('Notification updated successfully: ', $this->row->fresh()->toArray());
            } else {
                Log::error('Failed to update notification: ', $dataToUpdate);
                throw new \Exception(__('notification-update.failed'));
            }

            return $this->row->fresh();
        } catch (\Exception $e) {
            Log::error('Error updating notification: ', ['error' => $e->getMessage()]);
            throw $e;
        }
    }

    /**
     * Kiểm tra quyền cập nhật thông báo
     *
     * @throws \Exception
     */
    protected function checkAuthorization(): void
    {
        if (!$this->auth instanceof User) {
            throw new \Exception(__('notification-update.unauthorized'));
        }

        $user = $this->auth;

        // Root hoặc người gửi (sender) được phép cập nhật
        if ($user->hasRole('root') || $this->row->sender_id === $user->getAuthIdentifier()) {
            // Nếu thông báo là system, chỉ root được phép cập nhật
            if ($this->data['notification_type'] === 'system' && !$user->hasRole('root')) {
                throw new \Exception(__('notification-update.unauthorized-system'));
            }

            // Nếu thông báo là enterprise, kiểm tra enterprise_id
            if ($this->data['notification_type'] === 'enterprise' && !is_null($this->data['enterprise_id'])) {
                if ($this->data['enterprise_id'] !== $user->enterprise_id && !$user->hasRole('root')) {
                    throw new \Exception(__('notification-update.owner-enterprise-mismatch'));
                }
            }

            return;
        }

        throw new \Exception(__('notification-update.unauthorized'));
    }
}