<?php

namespace App\Domains\Notification\Action;

use App\Domains\Device\Model\Device;
use App\Domains\Display\Model\Display;

class PushMessageToDevicesAction extends PushMessageAbstract
{
    protected function pushMessage(): string
    {
        $data = $this->data();

        $deviceIds = $this->request?->input('device_ids', []) ?? [];
        $devices = Device::whereIn('id', $deviceIds)->get();

        if ($devices->isEmpty()) {
            return json_encode($data, JSON_UNESCAPED_UNICODE);
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
        } finally {
            sleep(1);
            $this->mqttService->disconnect();
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}