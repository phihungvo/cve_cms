<?php
declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Service\Controller;

use App\Domains\Campaign\Schedule\Action\Update as UpdateAction;
use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\Device\Model\Device;
use App\Domains\Playlist\Model\PlaylistModel;
use App\Domains\User\Model\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class Update
{
    protected Request $request;

    protected User $auth;

    protected Schedule $schedule;

    public function __construct($request, $auth, Schedule $schedule)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->schedule = $schedule;
    }

    public static function new($request, $auth, Schedule $schedule): self
    {
        return new self($request, $auth, $schedule);
    }

    public function data(): array
    {
        $playlists = PlaylistModel::byEnterprise()->pluck('name', 'id')->toArray();

        return [
            'schedule' => $this->schedule->load('enterprise'),
            'playlistOptions' => $playlists,
            'devices' => $this->devices(),
        ];

    }

    /**
     * @return \App\Domains\Device\Model\Collection\Device|Device[]|Collection
     */
    protected function devices(): Collection|array|\App\Domains\Device\Model\Collection\Device
    {
        return Device::query()
            ->whereDeviceHasDisplayWithPlaylist($this->schedule->playlist->id)
            ->with(
                'displays',
                function ($query) {
                    $query->where('playlist_id', $this->schedule->playlist->id);
                }
            )
            ->get();

    }

    public function update(): Schedule
    {
        $playlists = PlaylistModel::all()->pluck('id')->toArray();

        $data = $this->request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'playlist_id' => 'required|integer|in:'.implode(',', $playlists),
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'repeat' => 'nullable|boolean',
            'active' => 'nullable|boolean', // Thêm validation cho active
            'deviceIds' => 'nullable|array',
        ]);

        $data['repeat'] = isset($data['repeat']) && (bool)$data['repeat'];
        $data['active'] = isset($data['active']) ? (bool)$data['active'] : $this->schedule->active; // Giữ giá trị hiện tại nếu không có dữ liệu mới

        $action = new UpdateAction();

        return $action->handle($this->schedule, $data);
    }
}
