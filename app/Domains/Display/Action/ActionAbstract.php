<?php
declare(strict_types=1);

namespace App\Domains\Display\Action;

use App\Domains\Display\Model\Display as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var ?\App\Domains\Display\Model\Display
     */
    protected ?Model $row;
}
