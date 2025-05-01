<?php

namespace App\Domains\Solution\Action;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;
use App\Domains\Solution\Model\DeviceCvedixrtSolution as Model;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var Model|null
     *
     * @overide
     */
    protected ?Model $row;
}
