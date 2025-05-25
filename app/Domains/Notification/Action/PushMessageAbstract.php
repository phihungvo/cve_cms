<?php

namespace App\Domains\Notification\Action;

use App\Domains\Notification\Model\Notification;
use App\Services\Mqtt\MqttService;
use Illuminate\Http\Request;

abstract class PushMessageAbstract extends ActionAbstract
{
    protected ?Notification $notification;
    protected MqttService $mqttService;
    protected ?Request $request;

    public function handle(): string
    {
        $this->notification = $this->getNotification();
        $this->mqttService = app(MqttService::class);

        return $this->pushMessage();
    }

    abstract protected function pushMessage(): string|false;

    protected function getNotification(): ?Notification
    {
        $notificationId = $this->request?->get('notification_id');

        return Notification::with('enterprise', 'userNotifications.user')
            ->find($notificationId);
    }

    protected function data(): array
    {
        if (!$this->notification) {
            throw new \Exception('Notification not found');
        }

        return [
            'notification_id' => $this->notification->id,
            'title' => $this->title(),
            'content' => $this->content(),
            'notification_type' => $this->notification->notification_type,
            'enterprise_id' => $this->notification->enterprise_id,
            'target_group' => $this->notification->target_group ?? 'all',
        ];
    }

    protected function title(): string
    {
        return $this->notification->title ?? '';
    }

    protected function content(): string
    {
        return $this->notification->content ?? '';
    }
}