<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\License\Controller;

use App\Domains\User\Enterprise\License\Model\License as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    /**
     * @var ?\App\Domains\User\Enterprise\License\Model\License
     */
    protected ?Model $row;

    /**
     * @param int $id
     *
     * @return \App\Domains\User\Enterprise\License\Model\License
     */

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->firstOrFail();
    }

}
