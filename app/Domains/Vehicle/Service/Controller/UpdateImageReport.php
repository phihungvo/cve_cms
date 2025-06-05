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
            'fpp_by_date' => $this->fppReportByDate(),
        ];
    }

    protected function odoReportByDate(): Collection
    {
        $reports = Model::query()
            ->byVehicleId($this->rowReport->id)
            ->where('label', 'odo')
            ->with(['device'])
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->created_at)->addHours(7)->format('Y-m-d'); // Adjust for +7 timezone
            })
            ->map(function ($group) {
                return new Collection($group);
            })
            ->sortKeysDesc(); // Sort dates in descending order (most recent first)

        return $this->cache(fn() => new Collection($reports));
    }

    protected function fppReportByDate(): Collection
    {
        $reports = Model::query()
            ->byVehicleId($this->rowReport->id)
            ->where('label', 'fpp')
            ->with(['device'])
            ->get()
            ->groupBy(function ($item) {
                return \Carbon\Carbon::parse($item->created_at)->addHours(7)->format('Y-m-d'); // Adjust for +7 timezone
            })
            ->map(function ($group) {
                return new Collection($group);
            })
            ->sortKeysDesc(); // Sort dates in descending order (most recent first)

        return $this->cache(fn() => new Collection($reports));
    }
}
