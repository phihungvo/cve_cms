<?php

namespace App\Domains\Playlist\Action;

use App\Domains\Playlist\Model\PlaylistModel as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;

class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var Model|null
     *
     * @overide
     */
    protected ?Model $row;
}
