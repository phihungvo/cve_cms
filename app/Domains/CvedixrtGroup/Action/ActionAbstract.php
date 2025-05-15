<?php declare(strict_types=1);

namespace App\Domains\CvedixrtGroup\Action;

use App\Domains\CvedixrtGroup\Model\CvedixrtGroupModel as Model;
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
