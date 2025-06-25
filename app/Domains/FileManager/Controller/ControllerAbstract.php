<?php declare(strict_types=1);

namespace App\Domains\FileManager\Controller;

use App\Domains\FileManager\Model\FileManager as Model;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

abstract class ControllerAbstract extends ControllerWebAbstract
{
    protected ?Model $row;

    protected function row(int $id): Model
    {
        return $this->row = Model::query()
            ->byId($id)
            ->firstOr(fn () => $this->exceptionNotFound(__('file-manager.error.not-found')));
    }
}
