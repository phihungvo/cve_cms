<?php declare(strict_types=1);

namespace App\Domains\Display\ControllerApi;

use App\Domains\CoreApp\Controller\ControllerApiAbstract as CoreControllerApiAbstract;
use App\Domains\Display\Model\Display as Model;

abstract class ControllerApiAbstract extends CoreControllerApiAbstract
{
    /**
     * @var ?\App\Domains\Display\Model\Display
     */
    protected ?Model $row;

    /**
     * @param int $id
     *
     * @return \App\Domains\Display\Model\Display
     */
    protected function row(int $id): Model
    {
        $this->row = Model::query()
            ->byId($id)
            ->byUserOrManager($this->auth)
            ->firstOr(fn () => $this->exceptionNotFound(__('display.error.not-found')));

        return $this->row;
    }
}
