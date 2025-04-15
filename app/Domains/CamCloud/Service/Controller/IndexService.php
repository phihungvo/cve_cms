<?php

namespace App\Domains\CamCloud\Service\Controller;

use App\Domains\CamCloud\Model\Camera;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class IndexService extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->filters();
    }

    protected function filters(): void
    {

    }

    /**
     * @return mixed
     */
    public function data(): mixed
    {
        return $this->dataCore() + [
            'list' => $this->list(),
            'devices' => $this->getDevices(),
            'enterprises' => $this->enterprises(),
        ];
    }

    /**
     * Lấy danh sách các camera mà người dùng có quyền truy cập.
     *
     * @return Collection
     */
    protected function list(): Collection
    {
        return Camera::query()
            ->with('device')
            ->roleOwner()
            ->get();
    }

    protected function getDevices()
    {
        return [];
    }

    /**
     * Lấy ra danh sách các enterprise mà người dùng có quyền truy cập.
     *
     * Nếu người dùng là root, sẽ lấy tất cả các enterprise không bị xóa.
     *
     * Nếu người dùng không phải là root, sẽ trả về một collection rỗng.
     *
     * @return Collection
     */
    protected function enterprises(): Collection
    {
        if (auth()->user()->isRoleRoot()) {
            return Enterprise::query()
                ->withoutTrashed()
                ->get();
        }

        return collect();
    }
}
