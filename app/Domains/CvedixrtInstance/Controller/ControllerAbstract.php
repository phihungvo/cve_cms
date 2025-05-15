<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Controller;

use App\Domains\CvedixrtInstance\Model\CvedixrtInstanceModel as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;
use App\Exceptions\NotFoundException;

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
            // TODO: define func byId in ModelAbstract
            ->byId($id)
            // ->roleRoot()
            ->firstOr(fn () => $this->exceptionNotFound(__('cvedixrt-instance-update.error.not-found')));
    }
}
