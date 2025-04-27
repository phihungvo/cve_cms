<?php

declare(strict_types=1);

namespace App\Domains\Notification\Action;

use App\Domains\Notification\Model\Notification;
use App\Domains\User\Role\Model\Role;
use Illuminate\Support\Facades\Log;

class Update extends ActionAbstract
{
    protected array $data;
    protected Notification $row;

    public function handle(Notification $notification, array $data): Notification
    {
        $this->row = $notification;
        $this->data = $data;

        return $this->updateNotification();
    }

    protected function updateNotification(): Notification
    {
        Log::info('Updating notification with data: ', $this->data);

        try {
            // Kiểm tra dữ liệu hợp lệ
            if (empty($this->data['title']) || empty($this->data['content'])) {
                throw new \Exception(__('notification-update.invalid-data'));
            }

            // Kiểm tra target_group (nếu có) có hợp lệ không
            if (!empty($this->data['target_group'])) {
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
}