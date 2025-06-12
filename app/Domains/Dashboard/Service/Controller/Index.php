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
use Carbon\Carbon;

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

        // Log::info('Dashboard data params', [
        //     'isRoot' => $isRoot,
        //     'enterpriseId' => $enterpriseId,
        //     'userId' => $this->auth->id,
        // ]);

        $counts = [
            'devices' => $this->getCount(DeviceModel::query(), $isRoot, $enterpriseId, 'devices'),
            'users' => $this->getCount(UserModel::query(), $isRoot, $enterpriseId, 'users'),
            'enterprises' => $isRoot ? $this->getEnterpriseCount() : 0,
            'campaigns' => $this->getCount(CampaignModel::query(), $isRoot, $enterpriseId, 'campaigns'),
            'media' => $this->getCount(MediaModel::query(), $isRoot, $enterpriseId, 'media'),
            'playlists' => $this->getCount(PlaylistModel::query(), $isRoot, $enterpriseId, 'playlists'),
            'schedules' => $this->getCount(ScheduleModel::query(), $isRoot, $enterpriseId, 'schedules'),
        ];

        $chartData = $this->getChartData($isRoot, $enterpriseId);

        // Log::info('Chart data returned', [
        //     'chartData' => $chartData,
        // ]);

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
            'chart_data' => $chartData,
        ];
    }

    protected function getCount($query, bool $isRoot, ?int $enterpriseId, string $modelKey): int
    {
        $modelClass = get_class($query->getModel());
        $table = $query->getModel()->getTable();
        $cacheKey = "count_{$modelClass}_{$modelKey}_" . ($isRoot ? 'root_' : 'nonroot_') . ($enterpriseId ?? 'all') . '_user_' . $this->auth->id;

        // Log::info('Cache key generated', [
        //     'modelKey' => $modelKey,
        //     'cacheKey' => $cacheKey,
        // ]);

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

        // Log::info('getCount query', [
        //     'modelKey' => $modelKey,
        //     'table' => $table,
        //     'sql' => $countQuery->toSql(),
        //     'bindings' => $countQuery->getBindings(),
        //     'count' => $count,
        //     'enterpriseId' => $enterpriseId,
        //     'isRoot' => $isRoot,
        //     'userId' => $this->auth->id,
        //     'hasEnterpriseIdColumn' => $hasEnterpriseIdColumn,
        //     'usesSoftDeletes' => $usesSoftDeletes,
        // ]);

        return $count;
    }

    protected function getEnterpriseCount(): int
    {
        $cacheKey = 'enterprise_count_' . $this->auth->id;
        $query = EnterpriseModel::query()->whereNull('deleted_at');
        $countQuery = $query->selectRaw('COUNT(*) as count');
        $countResult = $countQuery->first();

        $count = $countResult ? (int) $countResult->count : 0;

        // Log::info('getEnterpriseCount query', [
        //     'sql' => $countQuery->toSql(),
        //     'bindings' => $countQuery->getBindings(),
        //     'count' => $count,
        //     'userId' => $this->auth->id,
        // ]);

        return $count;
    }

    protected function getChartData(bool $isRoot, ?int $enterpriseId): array
    {
        try {
            // Lấy start_date và end_date từ request, nếu không có thì dùng mặc định 30 ngày
            $startDate = $this->request->input('start_date')
                ? Carbon::parse($this->request->input('start_date'))
                : Carbon::now()->subDays(30);
            $endDate = $this->request->input('end_date')
                ? Carbon::parse($this->request->input('end_date'))
                : Carbon::now();
            if ($startDate > $endDate) {
                $temp = $startDate;
                $startDate = $endDate;
                $endDate = $temp;
            }

            // Log::info('Chart date filter parameters', [
            //     'start_date' => $startDate->format('Y-m-d'),
            //     'end_date' => $endDate->format('Y-m-d'),
            //     'request' => $this->request->all(),
            // ]);

            $dates = collect();
            for ($date = clone $startDate; $date <= $endDate; $date->addDay()) {
                $dates->push($date->format('Y-m-d'));
            }

            $models = [
                'users' => ['model' => UserModel::class, 'label' => __('dashboard-index.users'), 'color' => '#1f2937'],
                'devices' => ['model' => DeviceModel::class, 'label' => __('dashboard-index.devices'), 'color' => '#3b82f6'],
                'campaigns' => ['model' => CampaignModel::class, 'label' => __('dashboard-index.campaigns'), 'color' => '#10b981'],
                'media' => ['model' => MediaModel::class, 'label' => __('dashboard-index.media'), 'color' => '#f59e0b'],
                'playlists' => ['model' => PlaylistModel::class, 'label' => __('dashboard-index.playlists'), 'color' => '#ef4444'],
                'schedules' => ['model' => ScheduleModel::class, 'label' => __('dashboard-index.schedules'), 'color' => '#8b5cf6'],
                'enterprises' => ['model' => EnterpriseModel::class, 'label' => __('dashboard-index.enterprises'), 'color' => '#ec4899'],
            ];

            $chartData = [
                'labels' => $dates->toArray(),
                'datasets' => [],
            ];

            foreach ($models as $key => $config) {
                $query = $config['model']::query();
                $table = (new $config['model'])->getTable();
                $hasEnterpriseIdColumn = Schema::hasColumn($table, 'enterprise_id');

                // Log::debug('Processing chart data for model', [
                //     'key' => $key,
                //     'table' => $table,
                //     'hasEnterpriseIdColumn' => $hasEnterpriseIdColumn,
                // ]);

                if ($hasEnterpriseIdColumn && $enterpriseId !== null && ($isRoot && $this->request->filled('enterprise_id') || !$isRoot)) {
                    $query->where('enterprise_id', $enterpriseId);
                }

                if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($config['model']))) {
                    $query->whereNull('deleted_at');
                }

                if ($key === 'users' && !$isRoot) {
                    $query->where('id', '!=', $this->auth->id);
                }

                $counts = [];
                foreach ($dates as $date) {
                    $countQuery = (clone $query)
                        ->whereDate('created_at', '>=', $startDate->format('Y-m-d'))
                        ->whereDate('created_at', '<=', $date);
                    $count = $countQuery->count();

                    // Debug chi tiết để kiểm tra dữ liệu
                    $rawData = $countQuery->get()->toArray();
                    // Log::debug('Count for date', [
                    //     'key' => $key,
                    //     'date' => $date,
                    //     'sql' => $countQuery->toSql(),
                    //     'bindings' => $countQuery->getBindings(),
                    //     'count' => $count,
                    //     'raw_data_count' => count($rawData),
                    //     'raw_data_sample' => array_slice($rawData, 0, 2), // Lấy 2 bản ghi đầu để kiểm tra
                    // ]);

                    $counts[] = $count;
                }

                $chartData['datasets'][] = [
                    'label' => $config['label'],
                    'data' => $counts,
                    'borderColor' => $config['color'],
                    'backgroundColor' => $config['color'] . '33',
                    'fill' => true,
                    'tension' => 0.4,
                ];
            }

            // Log::info('Chart data prepared', [
            //     'labels_count' => count($chartData['labels']),
            //     'datasets_count' => count($chartData['datasets']),
            //     'labels' => $chartData['labels'],
            //     'datasets' => array_map(function ($dataset) {
            //         return ['label' => $dataset['label'], 'data_count' => count($dataset['data'])];
            //     }, $chartData['datasets']),
            // ]);

            return $chartData;
        } catch (\Exception $e) {
            // Log::error('Error generating chart data', [
            //     'error' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString(),
            //     'request' => $this->request->all(),
            // ]);

            return [
                'labels' => [],
                'datasets' => [],
            ];
        }
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
