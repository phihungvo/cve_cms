<?php

namespace App\Domains\Solution\Controller;

use App\Domains\CoreApp\Controller\ControllerWebAbstract;
use App\Domains\Device\Model\Device;
use App\Domains\Solution\Model\DeviceCvedixrtSolution as Model;
use App\Exceptions\NotFoundException;

class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    protected ?Device $device;

    /**
     * @throws NotFoundException
     */
    protected function row(int $id)
    {
        return $this->row = Model::query()
            ->byId($id)
            ->firstOr( fn () => $this->exceptionNotFound('solution-index.error.not-found'));
    }

    protected function device(int $id)
    {
       return $this->device = Device::query()
                ->byId($id)
                ->first();
    }

}
