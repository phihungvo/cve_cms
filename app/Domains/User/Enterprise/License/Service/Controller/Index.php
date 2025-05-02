<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\License\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Domains\User\Enterprise\License\Model\Collection\License as Collection;
use App\Domains\User\Enterprise\License\Model\License as Model;

class Index extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        // Log::info('IndexController: Initialized', ['user_id' => $auth->id ?? null]);
    }

    public function data(): array
    {
        // Log::info('IndexController: Fetching data');
        $data = [
            ...$this->dataCore(),
            'services' => $this->list(),
        ];
        // Log::info('IndexController: Data prepared', ['services_count' => $data['services']->count()]);
        return $data;
    }

    /**
     * @return \App\Domains\User\Enterprise\License\Model\Collection\License
     */
    public function list(): Collection
    {
        // Log::info('IndexController: Fetching License list with trashed records');
        $services = Model::query()->withTrashed()->get();
        // Log::info('IndexController: License list fetched', [
        //     'total' => $services->count(),
        //     'trashed' => $services->filter(fn($service) => $service->trashed())->count()
        // ]);
        return new Collection($services->all());
    }
}