<?php

namespace App\Domains\User\Enterprise\Controller;

use App\Domains\CoreApp\Controller\ControllerWebAbstract;
use App\Domains\User\Enterprise\Model\Enterprise as Model;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->byUserOrManager($this->auth)
            ->firstOr(fn () => $this->exceptionNotFound(__('enterprise.error.not-found')));
    }
}
