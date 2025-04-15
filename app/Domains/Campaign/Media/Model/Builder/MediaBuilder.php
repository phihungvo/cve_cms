<?php declare(strict_types=1);

namespace App\Domains\Campaign\Media\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;
use App\Domains\User\Enterprise\Model\Enterprise;

class MediaBuilder extends BuilderAbstract
{
    # custom query builder
    /**
     * Select all Media nếu User là Root
     *
     * @return $this
     */
    public function roleRoot(): MediaBuilder
    {
        return $this;
    }

    /**
     * Select Medias thuộc sở hữu của Enterprise
     *
     * @return $this
     */
    public function roleOwner(): MediaBuilder
    {
        if (auth()->user()?->isOwner()) {
            return $this->where(Enterprise::FOREIGN, auth()->user()->enterprise_id);
        }

        return $this;
    }
}
