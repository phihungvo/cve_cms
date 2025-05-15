<?php declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Service\Controller;

use App\Domains\Campaign\Schedule\Model\Schedule as Model;
use App\Domains\CoreApp\Service\Controller\ControllerAbstract as ControllerAbstractCore;

abstract class ControllerAbstract extends ControllerAbstractCore
{
    protected Model $row;
}
