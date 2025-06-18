<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Action;

use App\Domains\CvedixtModel\Model\Media as Model;
use App\Domains\CoreApp\Action\ActionAbstract as ActionAbstractCore;

abstract class ActionAbstract extends ActionAbstractCore
{
    /**
     * @var ?\App\Domains\CvedixtModel\Model\Model
     */
    protected ?Model $row;
}
