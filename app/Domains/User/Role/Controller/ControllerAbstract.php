<?php declare(strict_types=1);

namespace App\Domains\User\Role\Controller;

use App\Domains\User\Role\Model\Role as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    /**
     * @var ?\App\Domains\User\Role\Model\Role
     */
    protected ?Model $row;

    /**
     * @param int $id
     *
     * @return \App\Domains\User\Role\Model\Role
     */
    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id);
    }
}
