<?php

namespace App\Domains\Playlist\Controller;

use App\Domains\Playlist\Model\PlaylistModel as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            // TODO: define func byId in ModelAbstract
            ->byId($id)
            ->roleRoot() // PlaylistBuilder->roleRoot()
            ->firstOr(fn () => $this->exceptionNotFound(__('playlist.error.not-found')));
    }
}
