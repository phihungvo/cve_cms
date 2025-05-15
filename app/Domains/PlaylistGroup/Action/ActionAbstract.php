<?php declare(strict_types=1);

namespace App\Domains\PlaylistGroup\Action;

use App\Domains\PlaylistGroup\Model\PlaylistGroupModel as Model;
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
