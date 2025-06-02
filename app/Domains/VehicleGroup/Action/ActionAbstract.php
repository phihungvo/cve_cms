<?php declare(strict_types=1);

namespace App\Domains\VehicleGroup\Action;

use App\Domains\VehicleGroup\Model\VehicleGroupModel as Model;
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
