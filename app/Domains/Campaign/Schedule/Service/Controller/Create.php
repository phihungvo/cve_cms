<?php
declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Service\Controller;

use App\Domains\Campaign\Schedule\Action\Create as CreateAction;
use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\Device\Model\Device;
use App\Domains\Playlist\Model\PlaylistModel;
use App\Domains\User\Model\User;
use Illuminate\Database\Eloquent\Collection;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Create
{
    protected Request $request;

    protected ?User $auth;

    public function __construct($request, $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new($request, $auth): self
    {
        return new self($request, $auth);
    }

    public function data(): array
    {

        $user = Auth::user();
        if (!$user) {
            Log::warning('No authenticated user found in CheckPermission middleware');

            return redirect()->guest('user/auth');
        }

        $userId = $user->id;
        $userPermission = session('userPermission_'.$userId, []);
        $allPermissions = $userPermission['all'] ?? [];
        if (isset($allPermissions['root'])) {
            $enterprises = Enterprise::pluck('name', 'id')->toArray();

            return [
                'playlistOptions' => $this->playlists(),
                'enterpriseOptions' => $enterprises,
                'devices' => $this->getDevices(),
            ];
        }

        return [
            'playlistOptions' => $this->playlists(),
            'devices' => $this->getDevices(),
        ];
    }

    public function create(): Schedule
    {
        $playlists = PlaylistModel::pluck('id')->toArray();

        $data = $this->request->validate([
            'name' => 'nullable|string',
            'description' => 'nullable|string',
            'playlist_id' => 'required|integer|in:'.implode(',', $playlists),
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'repeat' => 'nullable|boolean',
            'active' => 'nullable|boolean', // Thêm validation cho active
            'enterprise_id' => 'nullable|integer',
            'deviceIds' => 'nullable|array',
        ]);

        $user = Auth::user();
        if (!$user) {
            Log::warning('No authenticated user found in CheckPermission middleware');

            return redirect()->guest('user/auth');
        }

        $userId = $user->id;
        $userPermission = session('userPermission_'.$userId, []);
        $allPermissions = $userPermission['all'] ?? [];
        if (isset($allPermissions['root'])) {

            $enterprise = Enterprise::pluck('id')->toArray();
            $data['enterprise_id'] = $this->request->validate([
                'enterprise_id' => 'integer|in:'.implode(',', $enterprise),
            ])['enterprise_id'];

        }

        $data['repeat'] = isset($data['repeat']) && (bool)$data['repeat'];
        $data['active'] = !isset($data['active']) || (bool)$data['active']; // Mặc định active là true nếu không có giá trị

        $action = new CreateAction();

        return $action->handle($data);
    }

    protected function playlists(): array
    {
        return PlaylistModel::byEnterprise()
            ->whereNotIn('id', Schedule::pluck('playlist_id')->toArray())
            ->pluck('name', 'id')
            ->toArray();
    }

    /**
     * @return \App\Domains\Device\Model\Collection\Device|Device[]|Collection
     */
    protected function devices(): Collection|array|\App\Domains\Device\Model\Collection\Device
    {
        return Device::query()
//            ->where('enterprise_id', $this->auth->enterprise_id)
//            ->where('status', Device::STATUS_ACTIVE)
            ->whereDeviceTypeAlias('fpp')
            ->get();
    }

    protected function getDevices(): Collection|array|\App\Domains\Device\Model\Collection\Device
    {
        $playlistId = $this->request->input('playlist_id');

        if (!$playlistId) {
            return [];
        }

        return Device::query()
            ->whereExists(function ($query) use ($playlistId) {
                $query->select('id')
                    ->from('display')
                    ->whereColumn('display.device_id', 'device.id')
                    ->where('display.playlist_id', $playlistId);
            })
            ->get();
    }
}
