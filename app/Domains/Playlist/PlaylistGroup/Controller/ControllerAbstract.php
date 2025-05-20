<?php declare(strict_types=1);

namespace App\Domains\Playlist\PlaylistGroup\Controller;

use App\Domains\Playlist\PlaylistGroup\Model\PlaylistGroupModel as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    /**
     * @param  int  $id
     * @return Model
     * @throws NotFoundException
     */
    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->withTrashed()
            ->firstOr(fn () => $this->exceptionNotFound(__('playlist-group-update.error.not-found')));
    }
}
