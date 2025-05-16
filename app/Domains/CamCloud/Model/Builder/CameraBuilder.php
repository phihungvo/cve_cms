<?php declare(strict_types=1);

namespace App\Domains\CamCloud\Model\Builder;
use App\Domains\CoreApp\Model\Builder\BuilderAbstract;

class CameraBuilder extends BuilderAbstract
{
    # Khởi tạo các phương thức tùy chỉnh cho Eloquent Builder

    /**
     * Kiểm tra quyền "owner" và lấy các camera thuộc về enterprise của người dùng.
     *
     * @return $this
     */
    public function roleOwner(): self
    {
        if (auth()->user()?->isOwner() || auth()->user()?->enterprise_id) {
            return $this->whereHas('device', function ($query) {
                $query->where('enterprise_id', auth()->user()->enterprise_id);
            });
        }

        return $this;
    }
}
