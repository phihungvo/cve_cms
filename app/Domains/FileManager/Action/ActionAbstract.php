<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Action;

use App\Domains\FileManager\Model\FileManager as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var ?Model
     */
    protected ?Model $row;
}
