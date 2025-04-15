<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Media\Action;

use App\Domains\Campaign\Media\Model\Media as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var ?\App\Domains\Campaign\Media\Model\Media
     */
    protected ?Model $row;
}