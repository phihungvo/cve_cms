<?php declare(strict_types=1);

namespace App\Domains\CvedixInstance\Action;

use App\Domains\CvedixInstance\Model\CvedixInstanceModel as Model;
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
