<?php declare(strict_types=1);

namespace App\Domains\DeviceGroup\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;
use Illuminate\Support\Collection;

class DeviceGroupBuilder extends BuilderAbstract
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
        if (auth()->user()->isOwner()) {
            return $this->where('enterprise_id', auth()->user()->enterprise_id)
                ->withTrashed();
        }

        return $this;
    }

    /**
     * Nếu người dùng là root thì không cần kiểm tra enterprise_id
     *
     * Nếu không phải root thì kiểm tra enterprise_id
     *
     * @return self
     */
    public function whereByEnterprise(): self
    {
        if (auth()->user()->enterprise_id === null) {
            return $this;
        }

        return $this->where('enterprise_id', auth()->user()->enterprise_id);
    }

    public function whenCreateUpdate()
    {
        if (auth()->user()->enterprise_id === null) {
            return collect([]);
        }

        return $this;
    }
}
