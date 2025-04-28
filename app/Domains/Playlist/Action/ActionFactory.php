<?php

namespace App\Domains\Playlist\Action;

use App\Domains\Core\Action\ActionFactoryAbstract;
use App\Domains\Playlist\Model\PlaylistModel as Model;

class ActionFactory extends ActionFactoryAbstract
{
    protected ?Model $row;

    public function create(): Model
    {
        return $this->actionHandle(CreateAction::class, $this->validate()->create());
    }

    public function update(): Model
    {
        # call to ValidateFactoryAbstract::__call()
        # $this->validate($customSubdomain): gọi tới link{ActionFactoryAbstract}
        return $this->actionHandle(UpdateAction::class, $this->validate()->update());
    }

    /**
     * Soft Delete
     *
     * @return void
     */
    public function delete(): void
    {
        $this->actionHandle(DeleteAction::class);
    }

    /**
     * Force Delete
     *
     * @return void
     */
    public function forceDelete(): void
    {
        $this->actionHandle(ForceDeleteAction::class);
    }

    /**
     * Restore
     *
     * @return void
     */
    public function restore(): void
    {
        $this->actionHandle(RestoreAction::class);
    }

    public function pushMessage(): array
    {
        return $this->actionHandle(PushMessageAction::class);
    }

    public function pushMessageToDevices(): array
    {
        return $this->actionHandle(PushMessageToDevicesAction::class);
    }
}
