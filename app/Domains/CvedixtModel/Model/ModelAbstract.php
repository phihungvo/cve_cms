<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Model;

use App\Domains\CoreApp\Model\ModelAbstract as CoreModelAbstract;

abstract class ModelAbstract extends CoreModelAbstract
{
    /**
     * @var ?\App\Domains\CvedixtModel\Model\Model
     */
    protected ?Model $row;
}
