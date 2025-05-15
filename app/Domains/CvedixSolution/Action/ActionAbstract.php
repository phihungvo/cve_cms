<?php declare(strict_types=1);

namespace App\Domains\CvedixSolution\Action;

use App\Domains\CvedixSolution\Model\CvedixSolutionModel as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var Model|null
     *
     * @overide
     */
    protected ?Model $row;
}
