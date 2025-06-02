<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Action;

use App\Domains\ScheduleGroup\Model\ScheduleGroupModel as Model;
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
