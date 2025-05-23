<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Controller;

use App\Domains\ScheduleGroup\Model\ScheduleGroupModel as Model;
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
            ->byId($id)->withTrashed()
            ->firstOr(fn () => $this->exceptionNotFound(__('schedule-group-update.error.not-found')));
    }
}
