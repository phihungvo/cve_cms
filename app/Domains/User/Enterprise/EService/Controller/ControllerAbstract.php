<?php declare(strict_types=1);

namespace App\Domains\User\Permission\Controller;

use App\Domains\User\Permission\Model\Permission as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    /**
     * @var ?\App\Domains\User\Permission\Model\Permission
     */
    protected ?Model $row;

    /**
     * @param int $id
     *
     * @return \App\Domains\User\Permission\Model\Permission
     */

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->firstOrFail();
    }

}
