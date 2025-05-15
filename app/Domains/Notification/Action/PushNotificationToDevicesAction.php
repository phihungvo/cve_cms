<?php

namespace App\Domains\Notification\Action;

use App\Domains\Device\Model\Device;
use App\Domains\Notification\Model\DeviceNotification;

class PushNotificationToDevicesAction extends PushNotificationAbstract
{
    protected function pushNotification(): array
    {
        $data = $this->data();
        $device_ids = $this->request->input('device_ids');

        // Lấy các thiết bị được chọn
        $devices = Device::whereIn('id', $device_ids)->get()->map(function ($device) {
            return [
                'id' => $device->id,
                'serial' => $device->serial,
            ];
        });

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