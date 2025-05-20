<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Action;

use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceModel as Model;
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
