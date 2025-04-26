<?php declare(strict_types=1);

namespace App\Domains\Group\Service\Controller;

use App\Domains\Device\Model\Device;
use App\Domains\Group\Model\DeviceCvedixrtGroup as Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class IndexService extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth, ?Device $device)
    {
        $this->filters();
        $this->device = $device;
    }

    protected function filters(): void
    {

    }

    public function data(): array
    {
        return [
            'list'  => $this->getList(),
            'device' => $this->device,
        ];
    }

    protected function getList(): Collection
    {
        return Model::query()
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
