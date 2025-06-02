<?php
declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Service\Controller;

use App\Domains\Display\Model\Display;
use App\Domains\Playlist\Model\PlaylistModel;
use App\Domains\ScheduleGroup\Model\ScheduleGroupModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Support\Facades\Log;
use LaravelIdea\Helper\App\Domains\User\Enterprise\Model\_IH_Enterprise_C;

class Index
{
    protected $request;

    protected $auth;

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

        return [
            'schedules' => $this->getSchedules(), // list()
            'enterprises' => $this->enterprises(),
            'scheduleGroups' => $this->scheduleGroups(),
        ];
    }

    protected function getSchedules(): Collection
    {
        // schedule_group_id
        $query = Schedule::query()
            ->byEnterprise()  // Thêm scope byEnterprise để lọc theo enterprise_id
            ->withTrashed()
            ->with([PlaylistModel::TABLE])
            ->when($this->request->input('enterprise_id'), function ($query) {
                $query->where('enterprise_id', $this->request->input('enterprise_id'));
            })
            ->when($this->request->input('schedule_group_id'), function ($query) {
                $query->whereHas('scheduleGroups', function ($q) {
                    $q->where('schedule_group_id', $this->request->input('schedule_group_id'));
                });
            });
        if ($search = $this->request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas(PlaylistModel::TABLE, function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%"); // Thêm tìm kiếm theo description của playlist
                })
                    ->orWhereHas(Schedule::TABLE, function ($q) use ($search) {
                        $q->where('name', 'like', "%{$search}%")
                            ->orWhere('description', 'like', "%{$search}%");
                    });
            });
        }

        $user = Auth::user();
        if (!$user) {
            Log::warning('No authenticated user found in CheckPermission middleware');

            return redirect()->guest('user/auth');
        }

        $userId = $user->id;
        $userPermission = session('userPermission_' . $userId, []);
        $allPermissions = $userPermission['all'] ?? [];
        if (isset($allPermissions['root'])) {
            // Lấy danh sách schedules
            $schedules = $query->orderBy('created_at', 'asc')->get();

            // Xử lý từng item để thêm enterprise_name
            $schedules->each(function ($schedule) {
                if (!is_null($schedule->enterprise_id)) {
                    $enterprise = Enterprise::where('id', $schedule->enterprise_id)->first();
                    if ($enterprise) {
                        $schedule->enterprise_name = $enterprise->name;
                    }
                    $schedule->published_display_count = $schedule->displays()->where(Display::SCHEDULE_PUBLISHED, '!=', 0)->count();

                }
            });

            return $schedules;
        } else {
            return $query->orderBy('created_at', 'asc')->get();
        }

    }

    protected function enterprises(): Collection|_IH_Enterprise_C|array
    {
        return Enterprise::query()
            ->whereHas('Schedules')
            ->get();
    }

    protected function scheduleGroups(): Collection
    {
        return ScheduleGroupModel::query()
            ->whenEnterprise((int) $this->request->input('enterprise_id'))
            ->roleRoot()
            ->roleOwner()
            ->get();
    }
}