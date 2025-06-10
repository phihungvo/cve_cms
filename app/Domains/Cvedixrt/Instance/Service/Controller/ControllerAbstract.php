<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Service\Controller;
use App\Domains\CoreApp\Service\Controller\ControllerAbstract as ControllerAbstractCore;
use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceModel as Model;

abstract class ControllerAbstract extends ControllerAbstractCore
{
    protected ?Model $row;
}
