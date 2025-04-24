<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\Device as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;
use App\Domains\Device\Model\DeviceCvedixrtInstance;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var ?Model
     */
    protected ?Model $row;

    protected ?DeviceCvedixrtInstance $instance;
}
