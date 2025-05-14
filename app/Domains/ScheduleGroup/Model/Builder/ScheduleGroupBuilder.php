<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;

class ScheduleGroupBuilder extends BuilderAbstract
{
    # Khởi tạo các phương thức tuỳ chỉnh có Eloquent Builder

    public function whenEnterprise(?int $enterprise_id): self
    {
        return $this->when($enterprise_id, fn ($q) => $q->where('enterprise_id', $enterprise_id));
    }

    public function roleRoot()
    {
        if (auth()->user()->isRoleRoot()) {
            return $this->withTrashed();
        }

        return $this;
    }

    public function roleOwner(): self
    {
        if (auth()->user()->isOwner()) {
            return $this->where('enterprise_id', auth()->user()->enterprise_id)
                ->withTrashed();
        }

        return $this;
    }
}
