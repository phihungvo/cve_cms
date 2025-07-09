<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Controller;

use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;
use App\Exceptions\NotFoundException;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    /**
     * @param int $id
     *
     * @throws NotFoundException
     *
     * @return Model
     */
    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->firstOr(fn () => $this->exceptionNotFound(__('event-update.error.not-found')));
    }
}
