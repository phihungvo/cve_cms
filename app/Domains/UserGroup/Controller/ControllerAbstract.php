<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Controller;

use App\Domains\CoreApp\Controller\ControllerWebAbstract;
use App\Domains\UserGroup\Model\GroupModel;
use App\Exceptions\NotFoundException;


class ControllerAbstract extends ControllerWebAbstract
{
    protected ?GroupModel $row;

    /**
     * @param int $id
     *
     * @throws NotFoundException
     *
     * @return GroupModel
     */
    protected function row(int $id): GroupModel
    {
        return $this->row = GroupModel::query()
            ->byId($id)
            ->roleRoot() // GroupBuilder->roleRoot()
            ->firstOr(fn () => $this->exceptionNotFound(__('group-update.error.not-found')));
    }
}
