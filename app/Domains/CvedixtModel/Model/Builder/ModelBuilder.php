<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;
use App\Domains\User\Enterprise\Model\Enterprise;

class ModelBuilder extends BuilderAbstract
{
    /**
     * Select all Models nếu User là Root
     *
     * @return $this
     */
    public function roleRoot(): ModelBuilder
    {
        return $this;
    }

    /**
     * Select Models thuộc sở hữu của Enterprise
     *
     * @return $this
     */
    public function roleOwner(): ModelBuilder
    {
        if (auth()->user()?->isOwner() || auth()->user()?->enterprise_id) {
            return $this->where(Enterprise::FOREIGN_KEY, auth()->user()->enterprise_id);
        }

        return $this;
    }
}
