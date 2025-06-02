<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Service\Controller;

use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\ScheduleGroup\Model\ScheduleGroupModel as Model;
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

    public function data(): array
    {
        return [
            'enterprises' => $this->enterprises(),
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

    protected function enterprises()
    {
        return $this->cache(
            fn () => Enterprise::query()
                ->get()
        );
    }
}
