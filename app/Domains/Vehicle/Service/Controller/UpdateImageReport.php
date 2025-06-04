<?php

declare(strict_types=1);

namespace App\Domains\Vehicle\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Vehicle\Model\VehicleImageReport as Model;
use App\Domains\Vehicle\Model\Collection\VehicleImageReport as Collection;
use Illuminate\Support\Arr;

class UpdateImageReport extends ControllerAbstract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     * @param \App\Domains\Vehicle\Model\Vehicle $row
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth, protected Model $rowReport) {}

    /**
     * @return array
     */
    public function data(): array
    {
        return [
            'row' => $this->rowReport,
            'odo' => $this->odoReport(),
            'fpp' => $this->fppReport(),
        ];
    }

    /**
     * @return \App\Domains\Vehicle\Model\Collection\Vehicle
     */
    protected function odoReport(): Collection
    {
        return $this->cache(
            fn() => new Collection(
                Model::query()
                    ->byVehicleId($this->rowReport->id)
                    ->where('label', 'odo')
                    ->get()
            )
        );
    }

    /**
     * @return \App\Domains\Vehicle\Model\Collection\Vehicle
     */
    protected function fppReport(): Collection
    {
        return $this->cache(
            fn() => new Collection(
                Model::query()
                    ->byVehicleId($this->rowReport->id)
                    ->where('label', 'fpp')
                    ->get()
            )
        );
    }
}
