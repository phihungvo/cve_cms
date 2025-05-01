<?php

namespace App\Domains\Solution\Service\Controller;

use App\Domains\Device\Model\Device;
use App\Domains\Solution\Model\DeviceCvedixrtSolution as Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class IndexService extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected ?Device $device)
    {
        $this->filters();

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
