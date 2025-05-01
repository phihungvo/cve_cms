<?php declare(strict_types=1);

namespace App\Domains\Group\Service\Controller;

use App\Domains\CoreApp\Service\Controller\ControllerAbstract as ControllerAbstractCore;
use App\Domains\Device\Model\Device;


abstract class ControllerAbstract extends ControllerAbstractCore
{
    protected ?Device $device;
}
