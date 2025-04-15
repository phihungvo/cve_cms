<?php declare(strict_types=1);

namespace App\Domains\User\Role\Feature\Action;

use App\Domains\User\Role\Feature\Model\Feature as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var ?\App\Domains\User\Role\Feature\Model\Feature
     */
    protected ?Model $row;
}
