<?php declare(strict_types=1);

namespace App\Domains\DeviceGroup\Controller;

use App\Domains\DeviceGroup\Model\DeviceGroupModel as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)->withTrashed()
            //->roleRoot()
            ->firstOr(fn () => $this->exceptionNotFound(__('device-group-update.error.not-found')));
    }
}
