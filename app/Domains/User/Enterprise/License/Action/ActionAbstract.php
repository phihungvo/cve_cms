<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\License\Action;

use App\Domains\User\Enterprise\License\Model\License as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var ?\App\Domains\User\Enterprise\License\Model\License
     */
    protected ?Model $row;
}