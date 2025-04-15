<?php

namespace App\Domains\Playlist\Service\Controller;

use App\Domains\Device\Model\Device;
use App\Domains\Display\Model\Display;
use App\Domains\Playlist\Model\PlaylistModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected PlaylistModel $row)
    {
        $this->request();
    }

    /**
     * Data update Playlist
     *
     * @return array
     *
     * @Override
     */
    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            'row' => $this->row,
            'selectedMedias' => $this->row->medias()->get(),
            'isUpdate' => true,
            'devices' => $this->getDevices(),
            'deviceIds' => $this->selectedDevices(),
        ];
    }

    protected function getDevices(): Collection|array|\App\Domains\Device\Model\Collection\Device
    {
        return Device::query()
            ->with(['displays' => function ($query) {
                $query->select(
                    'id',
                    'device_id',
                    'schedule_id',
                    Display::PLAYLIST_PUBLISHED,
                    Display::SCHEDULE_PUBLISHED
                )
                    ->where('schedule_id', $this->row->id)->get();
            }])->get();
    }

    protected function selectedDevices(): array
    {
        return $this->row->devices()->pluck('device.id')->toArray();
    }
}
