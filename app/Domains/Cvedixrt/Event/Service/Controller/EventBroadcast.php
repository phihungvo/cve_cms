<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Service\Controller;

use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class EventBroadcast extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->filters();
    }

    protected function filters(): void
    {
        // Add filter logic here
    }

    /**
     * Lấy ra danh sách các events (bao gồm 'priority' từ 'instanceRule').
     *
     * @return Collection
     */
    public function data(): Collection
    {
        return CvedixrtEventModel::query()
            ->select('id')
            ->get();
    }

    /**
     * Lấy ra danh sách các events theo instance_id (chỉ lấy column id).
     *
     * @return Collection
     */
    public function dataSingle(): Collection
    {
        $instanceId = $this->request->input('instance_id');
        if (!$instanceId) {
            throw new InvalidArgumentException('Instance ID is required');
        }

        return CvedixrtEventModel::query()
            ->whereHas('instanceRule', function ($q) use ($instanceId) {
                $q->where('cvedixrt_instance_id', $instanceId);
            })
            ->select('id')
            ->get();
    }
}
