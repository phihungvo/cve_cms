<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Group\Action;

use App\Domains\Cvedixrt\Group\Model\CvedixrtGroupModel as Model;
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
