<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Service\Controller;

use App\Domains\Cvedixrt\Group\Model\CvedixrtGroupModel;
use App\Domains\Cvedixrt\Solution\Model\CvedixrtSolutionModel;
use Illuminate\Database\Eloquent\Collection;

abstract class CreateUpdateAbstractService extends ControllerAbstract
{
    /**
     * @return void
     */
    protected function request(): void
    {
        $this->requestMergeWithRow();
    }

    protected function dataCreateUpdate(): array
    {
        return [
            'solutions' => $this->solutions(),
            'groups' => $this->groups(),
        ];
    }

    protected function solutions(): Collection
    {
        return CvedixrtSolutionModel::query()
            ->get();
    }

    protected function groups(): Collection
    {
        return CvedixrtGroupModel::query()
            ->get();
    }
}
