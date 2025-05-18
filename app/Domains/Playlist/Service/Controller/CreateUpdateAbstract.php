<?php

namespace App\Domains\Playlist\Service\Controller;

use App\Domains\Campaign\Media\Model\Media as MediaModel;
use App\Domains\Device\Model\Device;
use App\Domains\PlaylistGroup\Model\PlaylistGroupModel;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Database\Eloquent\Collection;

abstract class CreateUpdateAbstract extends ControllerAbstract
{
    /**
     * @return void
     */
    protected function request(): void
    {
        $this->requestMergeWithRow();

    }

    protected function dataCreateUpdate(): array
    {
        return $this->dataCore() + [
            'medias' => $this->medias(),
            'enterprises' => $this->enterprises(),
            'devices' => $this->devices(),
            'playlistGroups' => $this->playlistGroups(),
        ];
    }

    protected function medias(): Collection
    {
        // trả về media collection
        return MediaModel::query()
            ->roleRoot() // custom query in MediaBuilder->roleRoot()
            ->roleOwner()
            ->get();
    }

    protected function enterprises(): Collection
    {
        return Enterprise::query()
            ->get();
    }

    /**
     * Chỉ trả về các thiết bị có device_type là fpp
     *
     * @return \App\Domains\Device\Model\Collection\Device
     */
    protected function devices(): \App\Domains\Device\Model\Collection\Device
    {
        return Device::query()
            ->whereDeviceTypeAlias('fpp')
            ->userHasPermission('access-playlist-create-any')
            ->get();
    }

    protected function playlistGroups()
    {
        return PlaylistGroupModel::query()
            ->whereByRoot() // custom query in MediaBuilder->roleRoot()
            ->whereByOwner()
            ->get();
    }
}
