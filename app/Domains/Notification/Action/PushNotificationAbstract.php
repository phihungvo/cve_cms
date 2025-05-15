<?php

namespace App\Domains\Notification\Action;

use App\Domains\Core\Action\ActionAbstract;
use App\Domains\Notification\Model\Notification;
use App\Services\Mqtt\MqttService;

abstract class PushNotificationAbstract extends ActionAbstract
{
    protected ?Notification $notification;
    protected MqttService $mqttService;

    public function handle(): array
    {
        $this->notification = $this->getNotification();
        $this->mqttService = app(MqttService::class);

        return $this->pushNotification();
    }

    abstract protected function pushNotification(): array;

    protected function getNotification(): ?Notification
    {
        $notificationId = $this->request->get('notification_id');
        return Notification::find($notificationId);
    }

    protected function data(): array
    {
        return [
            'notification_id' => $this->notification->id,
            'title' => $this->notification->title,
            'content' => $this->notification->content,
            'notification_type' => $this->notification->notification_type,
            'enterprise_id' => $this->notification->enterprise_id,
            'sender_id' => $this->notification->sender_id,
            'created_at' => $this->notification->created_at->toDateTimeString(),
        ];
    }
}