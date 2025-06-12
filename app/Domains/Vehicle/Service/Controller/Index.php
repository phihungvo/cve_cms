<?php

declare(strict_types=1);

namespace App\Domains\Vehicle\Service\Controller;

use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\VehicleGroup\Model\VehicleGroupModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Vehicle\Model\Vehicle as Model;
use App\Domains\Vehicle\Model\Collection\Vehicle as Collection;

class Index extends ControllerAbstract
{
    /**
     * @var bool
     */
    protected bool $userEmpty = true;

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->filters();
    }

    /**
     * @return void
     */
    protected function filters(): void
    {
        $this->filtersUserId();
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->dataCore() + [
            'list' => $this->list(),
            'enterprises' => $this->enterprises(),
            'bookmarkGroups' => $this->bookmarkGroups(),
        ];
    }

    /**
     * @return \App\Domains\Vehicle\Model\Collection\Vehicle
     */
    protected function list(): Collection
    {
        return $this->cache(
            fn() => Model::query()
                ->whenUserId($this->user()?->id)
                ->whenEnterprise((int)$this->request->input('enterprise_id'))
                ->when(
                    (int)$this->request->input('vehicle_group_id'),
                    fn($query, $vehicleGroupId) => $query->byVehicleGroupId($vehicleGroupId)
                )
                ->withAlarmsCount()
                ->withAlarmsNotificationsCount()
                ->withAlarmsNotificationsPendingCount()
                ->withDevicesCount()
                ->withTimezone()
                ->withUser()
                ->with(['latestImageReport' => fn($query) => $query->orderBy('created_at', 'desc')->first()]) // Fetch the latest image report
                ->list()
                ->get()
        );
    }

    protected function enterprises()
    {
        return $this->cache(
            fn() => Enterprise::query()
                ->get()
        );
    }

    protected function bookmarkGroups()
    {
        return $this->cache(
            fn() => VehicleGroupModel::query()
                ->whenEnterprise((int)$this->request->input('enterprise_id'))
                ->roleRoot()
                ->roleOwner()
                ->get()
        );
    }
}
