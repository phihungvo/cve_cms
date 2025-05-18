<?php declare(strict_types=1);

namespace App\Domains\DeviceGroup\Service\Controller;

use App\Domains\DeviceGroup\Model\DeviceGroupModel as Model;
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
        // Add filter logic here
    }

    public function data(): array
    {
        return [
            'enterprises' => $this->enterprise(),
            'list' => $this->list(),
        ];
    }

    protected function list(): Collection
    {
        return Model::query()
            ->roleRoot()
            ->roleOwner()
            ->when($this->request->input('enterprise_id'), function ($query) {
                $query->where('enterprise_id', $this->request->input('enterprise_id'));
            })
            ->get();
    }

    protected function enterprise()
    {
        return $this->cache(
            fn () => Enterprise::query()
                ->get()
        );
    }
}
