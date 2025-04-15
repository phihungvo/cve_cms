<?php declare(strict_types=1);

namespace App\Domains\Position\ControllerApi;

use App\Domains\CoreApp\Controller\ControllerApiAbstract as CoreControllerApiAbstract;
use App\Domains\Position\Model\Position as Model;

abstract class ControllerApiAbstract extends CoreControllerApiAbstract
{
    /**
     * @var ?\App\Domains\Position\Model\Position
     */
    protected ?Model $row;

    /**
     * @param int $id
     *
     * @return \App\Domains\Position\Model\Position
     */
    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->byUserOrManager($this->auth)
            ->firstOr(fn() => $this->exceptionNotFound(__('position.error.not-found')));
    }
}
