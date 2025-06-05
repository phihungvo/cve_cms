<?php declare(strict_types=1);

namespace App\Domains\User\Permission\Feature\Controller;

use App\Domains\User\Permission\Feature\Model\Feature as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    /**
     * @var ?\App\Domains\User\Permission\Feature\Model\Feature
     */

    protected ?Model $row;

    /**
     * @param int $id
     *
     * @return \App\Domains\User\Permission\Feature\Model\Feature
     */

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->first(); // Hoặc find($id)
    }
}
