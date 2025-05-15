<?php

namespace App\Domains\Notification\Action;

use App\Domains\Core\Action\ActionFactoryAbstract;
use App\Domains\Notification\Model\Notification as Model;

class ActionFactory extends ActionFactoryAbstract
{
    protected ?Model $row;

    public function pushNotification(): array
    {
        return $this->actionHandle(PushNotificationAction::class);
    }

    public function pushNotificationToDevices(): array
    {
        return $this->actionHandle(PushNotificationToDevicesAction::class);
    }
}