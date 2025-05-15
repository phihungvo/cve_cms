<?php

namespace App\Domains\PlaylistGroup\Controller;

use App\Domains\PlaylistGroup\Model\PlaylistGroupModel as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->withTrashed()
            ->firstOr(fn () => $this->exceptionNotFound(__('playlist-group-update.error.not-found')));
    }
}
