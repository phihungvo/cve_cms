<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Service\Controller;
use App\Domains\CoreApp\Service\Controller\ControllerAbstract as ControllerAbstractCore;
use App\Domains\CvedixrtInstance\Model\CvedixrtInstanceModel as Model;

abstract class ControllerAbstract extends ControllerAbstractCore
{
    protected ?Model $row;
}
