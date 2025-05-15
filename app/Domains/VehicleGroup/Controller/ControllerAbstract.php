<?php

namespace App\Domains\VehicleGroup\Controller;

use App\Domains\VehicleGroup\Model\VehicleGroupModel as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)->withTrashed()
            ->firstOr(fn () => $this->exceptionNotFound(__('vehicle-group-update.error.not-found')));
    }
}
