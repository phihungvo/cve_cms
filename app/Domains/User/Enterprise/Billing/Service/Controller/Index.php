<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Domains\User\Enterprise\Billing\Model\Collection\Billing as Collection;
use App\Domains\User\Enterprise\Billing\Model\Billing as Model;

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
            'billings' => $this->list(),
        ];
        // Log::info('IndexController: Data prepared', ['billings_count' => $data['billings']->count()]);
        return $data;
    }

    public function list(): Collection
    {
        // Log::info('IndexController: Fetching Billing list with trashed records');
        $billings = Model::with(['license.service', 'license.enterprise'])->withTrashed()->get()->map(function ($billing) {
            $billingData = array_merge(
                $billing->toArray(),
                [
                    'license' => $billing->license ? $billing->license->toArray() : null,
                    'service' => $billing->license && $billing->license->service ? $billing->license->service->toArray() : null,
                    'enterprise' => $billing->license && $billing->license->enterprise ? $billing->license->enterprise->toArray() : null,
                ]
            );

            return $billingData;
        });

        Log::info('billings IndexController:', [$billings]);

        // Log::info('IndexController: Billing list fetched', [
        //     'total' => $billings->count(),
        //     'trashed' => $billings->filter(fn($billing) => $billing->trashed())->count()
        // ]);
        return new Collection($billings->all());
    }
}