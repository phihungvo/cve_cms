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
    public function __construct(protected Request $request, protected Authenticatable $auth, protected Model $rowReport) {}

    public function data(): array
    {
        return [
            'row' => $this->rowReport,
            'odo_by_date' => $this->odoReportByDate(),
            'fpp' => $this->fppReport(),
        ];
    }

    protected function odoReportByDate(): Collection
    {
        $reports = Model::query()
            ->byVehicleId($this->rowReport->id)
            ->where('label', 'odo')
            ->with(['device']) // Eager-load relationships if needed
            ->get()
            ->groupBy(function ($item) {
                return $item->created_at->format('Y-m-d'); // Nhóm theo ngày (YYYY-MM-DD)
            })
            ->map(function ($group) {
                return new Collection($group); // Wrap each group in custom Collection
            });

        // Wrap the grouped result in the custom Collection
        return $this->cache(fn() => new Collection($reports));
    }

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
