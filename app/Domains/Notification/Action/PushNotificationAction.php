<?php

namespace App\Domains\Notification\Action;

use App\Domains\Device\Model\Device;
use App\Domains\Notification\Model\DeviceNotification;
use Exception;

class PushNotificationAction extends PushNotificationAbstract
{
    protected function pushNotification(): array
    {
        $data = $this->data();

        if (!$this->notification) {
            throw new Exception('Notification not found');
        }

        // Lấy tất cả thiết bị thuộc enterprise của notification
        $devices = Device::whereHas('displays', function ($query) {
            $query->where('enterprise_id', $this->notification->enterprise_id);
        })->get()->map(function ($device) {
            return [
                'id' => $device->id,
                'serial' => $device->serial,
            ];
        });

        if ($devices->isEmpty()) {
            throw new Exception('No devices found for this enterprise');
        }

        try {
            $this->mqttService->connect();

            foreach ($devices as $device) {
                $topic = 'device/' . $device['serial'] . '/fpp/notification';
                $message = json_encode($data, JSON_UNESCAPED_UNICODE);
                $this->mqttService->publish($topic, $message, 1, false);

                // Lưu trạng thái gửi
                DeviceNotification::create([
                    'notification_id' => $this->notification->id,
                    'device_id' => $device['id'],
                    'is_sent' => true,
                    'sent_at' => now(),
                ]);
            }
        } catch (Exception $e) {
            throw new Exception('Failed to publish MQTT message: ' . $e->getMessage());
        } finally {
            sleep(1);
            $this->mqttService->disconnect();
        }

        return [
            'status' => 'success',
            'sent_devices' => $devices->pluck('id')->toArray(),
            'data' => $data,
        ];
    }
}