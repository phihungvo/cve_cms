<?php declare(strict_types=1);

namespace App\Domains\CvedixSolution\Controller;

use App\Domains\CvedixSolution\Model\CvedixSolutionModel as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;
use App\Exceptions\NotFoundException;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    /**
     * @param  int  $id
     * @return Model
     * @throws NotFoundException
     */
    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            // TODO: define func byId in ModelAbstract
            ->byId($id)
            // ->roleRoot()
            ->firstOr(fn () => $this->exceptionNotFound(__('cvedix-solution-update.error.not-found')));
    }
}
