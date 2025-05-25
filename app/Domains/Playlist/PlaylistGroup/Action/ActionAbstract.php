<?php declare(strict_types=1);

namespace App\Domains\Playlist\PlaylistGroup\Action;

use App\Domains\Playlist\PlaylistGroup\Model\PlaylistGroupModel as Model;

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
