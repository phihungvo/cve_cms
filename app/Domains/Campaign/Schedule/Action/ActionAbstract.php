<?php
declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Action;

use App\Domains\Campaign\Schedule\Model\Schedule as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var ?\App\Domains\Campaign\Schedule\Model\Schedule
     */
    protected ?Model $row;
}
