<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use App\Domains\Device\Model\DeviceType;
use App\Domains\DeviceGroup\Model\DeviceGroupModel;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Model\User;
use App\Domains\Vehicle\Model\Vehicle;
use Illuminate\Support\Collection;

abstract class CreateUpdateAbstract extends ControllerAbstract
{
    /**
     * @return void
     */
    protected function request(): void
    {
        $this->requestMergeWithRow([
            'code' => ($this->row->code ?? helper()->uuid()),
            'user_id' => $this->user()->id,
        ]);
    }

    /**
     * @return array
     */
    protected function dataCreateUpdate(): array
    {
        return $this->dataCore() + [
            'device_types' => $this->deviceTypes(),
            'enterprises' => $this->enterprises(),
            'vehicles' => $this->listVehicles(), // Bookmark
            //                'listUser' => $this->listUsers(),
            'deviceGroups' => $this->deviceGroups(),
        ];
    }

    /**
     * @return array
     */
    protected function deviceTypes(): array
    {
        return DeviceType::all()->map(function ($deviceType) {
            return [
                'id' => $deviceType->id,
                'name' => $deviceType->name,
            ];
        })->toArray();
    }

    /**
     * @return Collection
     */
    protected function enterprises(): Collection
    {
        // case root
        if ($this->auth->isRoot()) {
            return Enterprise::all()->map(function ($enterprise) {
                return [
                    'id' => $enterprise->id,
                    'name' => $enterprise->name,
                ];
            });
        } else {
            // case owner enterprise
            return collect([
                [
                    'id' => $this->auth->enterprise->id,
                    'name' => $this->auth->enterprise->name,
                ],
            ]);
        }
    }

    /**
     * @return Collection
     */
    //    protected function listUsers(): Collection
    //    {
    //        if ($this->auth->isRoot()) {
    //            if ($this->request->input('enterprise_id')) {
    //                $this->users = User::where('enterprise_id', $this->request->input('enterprise_id'))->get();
    //            } else {
    //                $this->users = User::get();
    //            }
    //            return $this->users->map(function ($user) {
    //                return [
    //                    'id' => $user->id,
    //                    'name' => $user->name,
    //                ];
    //            });
    //        } else {
    //            return collect([
    //                [
    //                    'id' => $this->auth->id,
    //                    'name' => $this->auth->name,
    //                ]
    //            ]);
    //        }
    //    }

    protected function listVehicles(): Collection
    {
        if ($this->auth->isRoot()) {
            // role root
            if ($this->request->input('enterprise_id')) {
                $userIds = User::where('enterprise_id', $this->request->input('enterprise_id'))->pluck('id');
                $this->vehicles = Vehicle::whereIn('user_id', $userIds)->get();
            } else {
                $this->vehicles = Collect([]);
            }

            return $this->vehicles->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->name,
                ];
            });
        } elseif ($this->auth->isOwner()) {
            //            root owner
            $userIds = User::where('enterprise_id', $this->auth->enterprise->id)->pluck('id');
            $this->vehicles = Vehicle::whereIn('user_id', $userIds)->get();

            return $this->vehicles->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->name,
                ];
            });
        } else {
            // case user
            $this->vehicles = Vehicle::where('user_id', $this->auth->id)->get();

            return $this->vehicles->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'name' => $vehicle->name,
                ];
            });
        }
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function deviceGroups(): Collection
    {
        if ($this->auth->enterprise_id == null) {
            // role root
            if ($this->request->input('enterprise_id')) {
                $this->deviceGroups = DeviceGroupModel::query()
                    ->where('enterprise_id', $this->request->input('enterprise_id'))
                    ->get();
            } else {
                $this->deviceGroups = Collect([]);
            }

            return $this->deviceGroups;
        } else {
            // root owner
            $this->deviceGroups = DeviceGroupModel::query()
                ->where('enterprise_id', $this->auth->enterprise->id)
                ->get();

            return $this->deviceGroups;
        }
    }
}
