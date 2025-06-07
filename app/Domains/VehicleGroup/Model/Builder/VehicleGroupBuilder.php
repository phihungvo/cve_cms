<?php declare(strict_types=1);

namespace App\Domains\VehicleGroup\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;

class VehicleGroupBuilder extends BuilderAbstract
{
    # Khởi tạo các phương thức tuỳ chỉnh có Eloquent Builder

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
}
