<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Action;

use App\Domains\User\Enterprise\EService\Model\EService as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var ?\App\Domains\User\Enterprise\EService\Model\EService
     */
    protected ?Model $row;
}