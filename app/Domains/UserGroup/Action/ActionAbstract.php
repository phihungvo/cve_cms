<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Action;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;
use App\Domains\UserGroup\Model\GroupModel;

class ActionAbstract extends ActionAbstractCore{

    /**
     * @var GroupModel|null
     *
     * @overide
     */
    protected ?GroupModel $row;
}
