<?php declare(strict_types=1);

namespace App\Domains\User\Role\Feature\Controller;

use App\Domains\User\Role\Feature\Model\Feature as Model;
use App\Domains\User\Role\Model\Role;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->firstOrFail();
    }
}