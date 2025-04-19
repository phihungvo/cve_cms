<?php

declare(strict_types=1);

namespace App\Domains\Dashboard\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Alarm\Model\Alarm as AlarmModel;
use App\Domains\Alarm\Model\Collection\Alarm as AlarmCollection;
use App\Domains\AlarmNotification\Model\AlarmNotification as AlarmNotificationModel;
use App\Domains\AlarmNotification\Model\Collection\AlarmNotification as AlarmNotificationCollection;
use App\Domains\Position\Model\Collection\Position as PositionCollection;
use App\Domains\Trip\Model\Collection\Trip as TripCollection;
use App\Domains\Trip\Model\Trip as TripModel;
use App\Domains\Server\Model\Server as ServerModel;
use App\Domains\User\Enterprise\Model\Enterprise as EnterpriseModel;
use App\Domains\User\Model\User as UserModel;
use App\Domains\Device\Model\Device as DeviceModel;
use App\Domains\Campaign\Model\Campaign as CampaignModel;
use App\Domains\Campaign\Media\Model\Media as MediaModel;
use App\Domains\Playlist\Model\PlaylistModel as PlaylistModel;
use App\Domains\Campaign\Schedule\Model\Schedule as ScheduleModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class Index extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->filters();
    }

    protected function filters(): void
    {
        $this->filtersUserId();
        $this->filtersVehicleId();
        $this->filtersDeviceId();
    }

    public function data(): array
    {
        $isRoot = $this->auth->isRoleRoot();
        $enterpriseId = $isRoot && $this->request->filled('enterprise_id')
            ? (int) $this->request->input('enterprise_id')
            : ($isRoot ? null : ($this->auth->enterprise_id ? (int) $this->auth->enterprise_id : null));

        Log::info('getCount params', [
            'isRoot' => $isRoot,
            'enterpriseId' => $enterpriseId,
            'userId' => $this->auth->id,
        ]);

        $counts = [
            'devices' => $this->getCount(DeviceModel::query(), $isRoot, $enterpriseId, 'devices'),
            'users' => $this->getCount(UserModel::query(), $isRoot, $enterpriseId, 'users'),
            'enterprises' => $isRoot ? $this->getEnterpriseCount() : 0,
            'campaigns' => $this->getCount(CampaignModel::query(), $isRoot, $enterpriseId, 'campaigns'),
            'media' => $this->getCount(MediaModel::query(), $isRoot, $enterpriseId, 'media'),
            'playlists' => $this->getCount(PlaylistModel::query(), $isRoot, $enterpriseId, 'playlists'),
            'schedules' => $this->getCount(ScheduleModel::query(), $isRoot, $enterpriseId, 'schedules'),
        ];

        Log::info('Counts result', [
            'counts' => $counts,
        ]);

        return [
            'auth' => $this->auth,
            'user' => $this->user(),
            'logo_url' => $this->getLogo(),
            'vehicle' => $this->vehicle(),
            'device' => $this->device(),
            'onboarding' => $this->onboarding(),
            'server' => $this->server(),
            'users' => $this->users(),
            'users_multiple' => $this->usersMultiple(),
            'user_empty' => $this->userEmpty(),
            'vehicles' => $this->vehicles(),
            'vehicles_multiple' => $this->vehiclesMultiple(),
            'vehicle_empty' => $this->vehicleEmpty(),
            'devices' => $this->devices(),
            'devices_multiple' => $this->devicesMultiple(),
            'device_empty' => $this->deviceEmpty(),
            'trips' => $this->trips(),
            'trip' => $this->trip(),
            'trip_next_id' => $this->tripNextId(),
            'trip_previous_id' => $this->tripPreviousId(),
            'trip_alarm_notifications' => $this->tripAlarmNotifications(),
            'positions' => $this->positions(),
            'alarms' => $this->alarms(),
            'alarm_notifications' => $this->alarmNotifications(),
            'enterprises' => $isRoot ? EnterpriseModel::query()->get() : collect(),
            'counts' => $counts,
        ];
    }

    /**
     * Get count with enterprise filter for non-root users
     */
    protected function getCount($query, bool $isRoot, ?int $enterpriseId, string $modelKey): int
    {
        $modelClass = get_class($query->getModel());
        $table = $query->getModel()->getTable();
        $cacheKey = "count_{$modelClass}_{$modelKey}_" . ($isRoot ? 'root_' : 'nonroot_') . ($enterpriseId ?? 'all') . '_user_' . $this->auth->id;

        Log::info('Cache key generated', [
            'modelKey' => $modelKey,
            'cacheKey' => $cacheKey,
        ]);

        // Tạm thời bỏ qua cache để kiểm tra
        $usesSoftDeletes = in_array(
            \Illuminate\Database\Eloquent\SoftDeletes::class,
            class_uses_recursive($modelClass)
        );

        $hasEnterpriseIdColumn = Schema::hasColumn($table, 'enterprise_id');

        $q = clone $query;

        // Áp dụng filter enterprise_id nếu bảng có cột enterprise_id
        if ($hasEnterpriseIdColumn && $enterpriseId !== null) {
            if (($isRoot && $this->request->filled('enterprise_id')) || !$isRoot) {
                $q = $q->where('enterprise_id', $enterpriseId);
            }
        }

        // Đặc biệt cho model User: Không đếm user hiện tại nếu là non-root
        if ($modelClass === UserModel::class && !$isRoot) {
            $q = $q->where('id', '!=', $this->auth->id);
        }

        // Bỏ qua bản ghi đã xóa nếu model sử dụng SoftDeletes
        if ($usesSoftDeletes) {
            $q = $q->whereNull('deleted_at');
        }

        // Đảm bảo truy vấn là COUNT
        $countQuery = $q->selectRaw('COUNT(*) as count');
        $countResult = $countQuery->first();

        $count = $countResult ? (int) $countResult->count : 0;

        Log::info('getCount query', [
            'modelKey' => $modelKey,
            'table' => $table,
            'sql' => $countQuery->toSql(),
            'bindings' => $countQuery->getBindings(),
            'count' => $count,
            'enterpriseId' => $enterpriseId,
            'isRoot' => $isRoot,
            'userId' => $this->auth->id,
            'hasEnterpriseIdColumn' => $hasEnterpriseIdColumn,
            'usesSoftDeletes' => $usesSoftDeletes,
        ]);

        return $count;

        // Code cache ban đầu (được comment để kiểm tra)
        /*
        return $this->cache(
            function () use ($query, $isRoot, $enterpriseId, $modelClass, $modelKey, $table) {
                $usesSoftDeletes = in_array(
                    \Illuminate\Database\Eloquent\SoftDeletes::class,
                    class_uses_recursive($modelClass)
                );

                $hasEnterpriseIdColumn = Schema::hasColumn($table, 'enterprise_id');

                $q = clone $query;

                if ($hasEnterpriseIdColumn && $enterpriseId !== null) {
                    if (($isRoot && $this->request->filled('enterprise_id')) || !$isRoot) {
                        $q = $q->where('enterprise_id', $enterpriseId);
                    }
                }

                if ($modelClass === UserModel::class && !$isRoot) {
                    $q = $q->where('id', '!=', $this->auth->id);
                }

                if ($usesSoftDeletes) {
                    $q = $q->whereNull('deleted_at');
                }

                $countQuery = $q->selectRaw('COUNT(*) as count');
                $countResult = $countQuery->first();

                $count = $countResult ? (int) $countResult->count : 0;

                Log::info('getCount query', [
                    'modelKey' => $modelKey,
                    'table' => $table,
                    'sql' => $countQuery->toSql(),
                    'bindings' => $countQuery->getBindings(),
                    'count' => $count,
                    'enterpriseId' => $enterpriseId,
                    'isRoot' => $isRoot,
                    'userId' => $this->auth->id,
                    'hasEnterpriseIdColumn' => $hasEnterpriseIdColumn,
                    'usesSoftDeletes' => $usesSoftDeletes,
                ]);

                return $count;
            },
            $cacheKey,
            60
        );
        */
    }

    /**
     * Get enterprise count, considering soft deletes if applicable
     */
    protected function getEnterpriseCount(): int
    {
        $cacheKey = 'enterprise_count_' . $this->auth->id;

        // Tạm thời bỏ qua cache để kiểm tra
        $query = EnterpriseModel::query()->whereNull('deleted_at');
        $countQuery = $query->selectRaw('COUNT(*) as count');
        $countResult = $countQuery->first();

        $count = $countResult ? (int) $countResult->count : 0;

        Log::info('getEnterpriseCount query', [
            'sql' => $countQuery->toSql(),
            'bindings' => $countQuery->getBindings(),
            'count' => $count,
            'userId' => $this->auth->id,
        ]);

        return $count;

        // Code cache ban đầu
        /*
        return $this->cache(
            function () {
                $query = EnterpriseModel::query()->whereNull('deleted_at');
                $countQuery = $query->selectRaw('COUNT(*) as count');
                $countResult = $countQuery->first();

                $count = $countResult ? (int) $countResult->count : 0;

                Log::info('getEnterpriseCount query', [
                    'sql' => $countQuery->toSql(),
                    'bindings' => $countQuery->getBindings(),
                    'count' => $count,
                    'userId' => $this->auth->id,
                ]);

                return $count;
            },
            $cacheKey,
            60
        );
        */
    }

    protected function onboarding(): bool
    {
        return ($this->vehicles()->count() === 0)
            || ($this->devices()->count() === 0)
            || ($this->trips()->count() === 0);
    }

    protected function server(): ?ServerModel
    {
        if ($this->auth->adminMode() === false) {
            return null;
        }

        if ($this->onboarding() === false) {
            return null;
        }

        return $this->cache(
            fn() => ServerModel::query()
                ->enabled()
                ->first()
        );
    }

    protected function trips(): TripCollection
    {
        if ($this->vehicle() === null) {
            return new TripCollection();
        }

        return $this->cache(
            fn() => TripModel::query()
                ->byVehicleId($this->vehicle()->id)
                ->whenDeviceId($this->device()?->id)
                ->listSimple()
                ->limit(50)
                ->get()
        );
    }

    protected function trip(): ?TripModel
    {
        return $this->cache(
            fn() => $this->trips()->firstWhere('id', $this->request->input('trip_id'))
                ?: $this->trips()->first()
        );
    }

    protected function tripNextId(): ?int
    {
        if ($this->trip() === null) {
            return null;
        }

        return $this->cache(
            fn() => $this->trips()
                ->reverse()
                ->firstWhere('start_utc_at', '>', $this->trip()->start_utc_at)
                ?->id
        );
    }

    protected function tripPreviousId(): ?int
    {
        if ($this->trip() === null) {
            return null;
        }

        return $this->cache(
            fn() => $this->trips()
                ->firstWhere('start_utc_at', '<', $this->trip()->start_utc_at)
                ?->id
        );
    }

    protected function tripAlarmNotifications(): AlarmNotificationCollection
    {
        if ($this->trip() === null) {
            return new AlarmNotificationCollection();
        }

        return $this->cache(
            fn() => AlarmNotificationModel::query()
                ->byTripId($this->trip()->id)
                ->withAlarm()
                ->list()
                ->get()
        );
    }

    protected function positions(): PositionCollection
    {
        if ($this->trip() === null) {
            return new PositionCollection();
        }

        return $this->cache(
            fn() => $this->trip()
                ->positions()
                ->withCityState()
                ->list()
                ->get()
        );
    }

    protected function alarms(): AlarmCollection
    {
        if ($this->vehicle() === null) {
            return new AlarmCollection();
        }

        return $this->cache(
            fn() => AlarmModel::query()
                ->byVehicleId($this->vehicle()->id)
                ->enabled()
                ->list()
                ->get()
        );
    }

    protected function alarmNotifications(): AlarmNotificationCollection
    {
        if ($this->vehicle() === null) {
            return new AlarmNotificationCollection();
        }

        return $this->cache(
            fn() => AlarmNotificationModel::query()
                ->byVehicleId($this->vehicle()->id)
                ->whereClosedAt(false)
                ->withAlarm()
                ->withVehicle()
                ->withPosition()
                ->withTrip()
                ->list()
                ->get()
        );
    }

    protected function getLogo(): string
    {
        $user = Auth::user();

        if (!$user) {
            return '';
        }

        $userId = $user->id;
        $sessionKeyEnterprise = 'userEnterprise_' . $userId;
        $enterprise = session($sessionKeyEnterprise);

        if (!$enterprise) {
            return '';
        }

        return $this->cache(
            fn() => EnterpriseModel::query()
                ->where('id', $enterprise->id)
                ->select('logo_url')
                ->first()?->logo_url ?? ''
        );
    }
}
