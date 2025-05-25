<?php

namespace App\Domains\Notification\Action;

use App\Domains\Core\Action\ActionFactoryAbstract;
use App\Domains\Notification\Model\Notification;
use Illuminate\Http\Request;

class ActionFactory extends ActionFactoryAbstract
{
    protected ?Notification $row;
    protected ?Request $request;

    public function setDependencies(?Notification $row = null, ?Request $request = null): self
    {
        $this->row = $row;
        $this->request = $request;
        return $this;
    }

    public function pushMessage(): string|false
    {
        // Chuyển đổi dữ liệu request thành mảng
        $data = $this->request ? $this->request->all() : [];
        return $this->actionHandle(PushMessageAction::class, $data);
    }

    public function pushMessageToDevices(): string|false
    {
        // Chuyển đổi dữ liệu request thành mảng
        $data = $this->request ? $this->request->all() : [];
        return $this->actionHandle(PushMessageToDevicesAction::class, $data);
    }
}