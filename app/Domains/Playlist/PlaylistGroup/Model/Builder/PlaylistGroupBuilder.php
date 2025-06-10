<?php declare(strict_types=1);

namespace App\Domains\Playlist\PlaylistGroup\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;
use App\Domains\User\Enterprise\Model\Enterprise;

class PlaylistGroupBuilder extends BuilderAbstract
{
    public function roleRoot(): self
    {
        if (auth()->user()->isRoleRoot()) {
            return $this->withTrashed();
        }

        return $this;
    }

    public function roleOwner(): self
    {
        if (auth()->user()?->isOwner() || auth()->user()?->enterprise_id) {
            return $this->where('enterprise_id', auth()->user()->enterprise_id)
                ->withTrashed();
        }

        return $this;
    }

    public function whereByRoot(): self
    {
        return $this;
    }

    public function whereByOwner(): self
    {
        if (auth()->user()->isOwner()) {
            return $this->where(Enterprise::FOREIGN_KEY, auth()->user()->enterprise_id);
        }

        return $this;
    }

    public function whereByEnterprise(): self
    {
        if (auth()->user()->enterprise_id === null) {
            return $this;
        }

        return $this->where(Enterprise::FOREIGN_KEY, auth()->user()->enterprise_id);
    }

    public function filterByEnterpriseId(?int $enterpriseId): self
    {
        if ($enterpriseId) {
            return $this->where(Enterprise::FOREIGN_KEY, $enterpriseId);
        }

        return $this;
    }

    public function listSimple(): self
    {
        return $this->select('id', 'name')
            ->orderBy('name');
    }
}
