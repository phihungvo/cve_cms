<?php

namespace App\Services\Mqtt;

use App\Domains\Notification\Model\DeviceNotification;

class MqttAckHandler
{
    public function handleAck($topic, $message)
    {
        // Giả sử topic có dạng: device/{serial}/fpp/notification/ack
        preg_match('/device\/([^\/]+)\/fpp\/notification\/ack/', $topic, $matches);
        if (isset($matches[1])) {
            $serial = $matches[1];
            $device = \App\Domains\Device\Model\Device::where('serial', $serial)->first();
            if ($device) {
                $data = json_decode($message, true);
                $notificationId = $data['notification_id'] ?? null;

                if ($notificationId) {
                    DeviceNotification::where('notification_id', $notificationId)
                        ->where('device_id', $device->id)
                        ->update([
                            'is_read' => true,
                            'read_at' => now(),
                        ]);
                }
            }
        }
    }
}