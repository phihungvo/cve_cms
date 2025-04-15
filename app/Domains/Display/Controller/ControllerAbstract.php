<?php declare(strict_types=1);

namespace App\Domains\Display\Controller;

use App\Domains\CoreApp\Controller\ControllerWebAbstract;
use App\Domains\Display\Model\Display as DisplayAlias;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    /**
     * @var ?\App\Domains\Device\Model\Device
     */
    protected ?DisplayAlias $row;

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->firstOr(fn () => $this->exceptionNotFound(__('device.error.not-found')));
    }
}
