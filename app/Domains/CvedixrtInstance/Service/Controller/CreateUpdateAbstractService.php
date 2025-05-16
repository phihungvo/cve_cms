<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Service\Controller;

use App\Domains\Campaign\Media\Model\Media as MediaModel;
use App\Domains\CvedixrtGroup\Model\CvedixrtGroupModel;
use App\Domains\CvedixrtSolution\Model\CvedixrtSolutionModel;
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
