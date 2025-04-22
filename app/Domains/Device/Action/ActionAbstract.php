<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\Device as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;
use App\Domains\Device\Model\DeviceCveditInstance;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var ?\App\Domains\Device\Model\Device
     */
    protected ?Model $row;

    protected ?DeviceCveditInstance $instance;
}
