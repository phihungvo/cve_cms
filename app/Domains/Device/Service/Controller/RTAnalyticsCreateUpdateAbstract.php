<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use App\Domains\Group\Model\DeviceCvedixrtGroup;
use App\Domains\Solution\Model\DeviceCvedixrtSolution;
use Illuminate\Database\Eloquent\Collection;

abstract class RTAnalyticsCreateUpdateAbstract extends ControllerAbstract
{
    /**
     * @return void
     */
    protected function request(): void
    {
        $this->requestMergeWithRow([
            'uuid' => ($this->input['uuid'] ?? helper()->uuid()),
        ]);
    }

    /**
     * @return array
     */
    protected function dataCreateUpdate(): array
    {
        return [
            'row' => $this->row,
            //            'uuid' => ($this->input['uuid'] ?? helper()->uuid()),
            'solutions' => $this->solutions(),
            'groups' => $this->groups(),
        ];
    }

    /**
     * @return Collection
     */
    protected function solutions(): Collection
    {
        return DeviceCvedixrtSolution::query()
            ->get();
    }

    /**
     * @return Collection
     */
    protected function groups(): Collection
    {
        return DeviceCvedixrtGroup::query()
            ->get();
    }
}
