<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\ControllerApi;

use App\Domains\CoreApp\Controller\ControllerApiAbstract as CoreControllerApiAbstract;
use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel as Model;
use App\Exceptions\NotFoundException;

abstract class ControllerApiAbstract extends CoreControllerApiAbstract
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
            ->firstOr(fn () => $this->exceptionNotFound(__('cvedixrt_event.error.not-found')));
    }
}
