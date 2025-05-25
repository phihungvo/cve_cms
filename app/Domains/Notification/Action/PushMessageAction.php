<?php

namespace App\Domains\Notification\Action;

use App\Domains\Device\Model\Device;
use App\Domains\Display\Model\Display;
use Exception;

class PushMessageAction extends PushMessageAbstract
{
    protected function pushMessage(): string|false
    {
        $data = $this->data();

        // Lấy danh sách thiết bị và display tương ứng
        $devices = $this->getDevices();

        if ($devices->isEmpty()) {
            return false;
        }

        try {
            $this->mqttService->connect();

            foreach ($devices as $device) {
                $topic = 'device/' . $device->serial . '/fpp/notification/add';
                $message = json_encode($data, JSON_UNESCAPED_UNICODE);
                $this->mqttService->publish($topic, $message, 1, false);

                // Cập nhật notification_published trong bảng display
                Display::where('device_id', $device->id)
                    ->update(['notification_published' => 1]);
            }
        } catch (Exception $e) {
            throw new Exception('Failed to push notification: ' . $e->getMessage());
        } finally {
            sleep(1);
            $this->mqttService->disconnect();
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    protected function getDevices()
    {
        $query = Device::query();

        if ($this->notification->enterprise_id) {
            $query->whereHas('displays', function ($q) {
                $q->where('enterprise_id', $this->notification->enterprise_id);
            });
        }

        if (!empty($this->notification->userNotifications)) {
            $userIds = $this->notification->userNotifications->pluck('user_id')->toArray();
            $query->whereHas('displays', function ($q) use ($userIds) {
                $q->whereIn('user_id', $userIds);
            });
        }

        return $query->get();
    }
}