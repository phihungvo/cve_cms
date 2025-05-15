<?php

namespace App\Domains\Notification\Action;

use App\Domains\CoreApp\Action\ActionAbstract;
use App\Domains\Notification\Model\DeviceNotification;
use App\Domains\Notification\Model\Notification;
use App\Services\Mqtt\MqttService;

class ResendNotificationAction extends ActionAbstract
{
    protected MqttService $mqttService;

    public function handle(int $notificationId): array
    {
        $this->mqttService = app(MqttService::class);
        $notification = Notification::findOrFail($notificationId);
        $deviceNotifications = DeviceNotification::where('notification_id', $notificationId)
            ->where('is_read', false)
            ->with('device')
            ->get();

        $data = [
            'notification_id' => $notification->id,
            'title' => $notification->title,
            'content' => $notification->content,
            'notification_type' => $notification->notification_type,
            'enterprise_id' => $notification->enterprise_id,
            'sender_id' => $notification->sender_id,
            'created_at' => $notification->created_at->toDateTimeString(),
        ];

        $resentDevices = [];

        try {
            $this->mqttService->connect();

            foreach ($deviceNotifications as $deviceNotification) {
                $device = $deviceNotification->device;
                $topic = 'device/' . $device->serial . '/fpp/notification';
                $message = json_encode($data, JSON_UNESCAPED_UNICODE);
                $this->mqttService->publish($topic, $message, 1, false);

                // Cập nhật trạng thái gửi
                $deviceNotification->update([
                    'is_sent' => true,
                    'sent_at' => now(),
                ]);

                $resentDevices[] = $device->id;
            }
        } finally {
            sleep(1);
            $this->mqttService->disconnect();
        }

        return [
            'status' => 'success',
            'resent_devices' => $resentDevices,
        ];
    }
}