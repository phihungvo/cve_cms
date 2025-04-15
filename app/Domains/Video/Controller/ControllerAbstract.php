<?php

declare(strict_types=1);

namespace App\Domains\Video\Controller;

use App\Domains\Video\Model\Video as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    /**
     * @var ?\App\Domains\Video\Model\Video
     */
    protected ?Model $row = null;

    /**
     * @param int $id
     *
     * @return \App\Domains\Video\Model\Video
     */
    protected function row(int $id): Model
    {
        $this->row = Model::query()
            ->byId($id)
            // ->byUserOrManager($this->auth)
            ->firstOr(fn() => $this->exceptionNotFound(__('video.error.not-found')));
        return $this->row;
    }
}
