<?php declare(strict_types=1);

namespace App\Domains\User\Service\Controller;

use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Model\User as Model;
use App\Domains\UserGroup\Model\GroupModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class IndexService extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->filters();
    }

    protected function filters()
    {
    }

    public function data(): array
    {
        return [
            'list' => $this->list(),
            'groups' => $this->groups(),
            'enterprises' => $this->enterprises(),
        ];
    }

    protected function list(): Collection
    {
        return Model::query()
            ->roleRoot()
            ->roleOwner()
            ->filterByUserPermission('access-user-list')
            ->when($this->request->input('enterprise_id'), function ($query) {
                $query->where('enterprise_id', $this->request->input('enterprise_id'));
            })
            ->when($this->request->input('group_id'), function ($query) {
                $query->whereHas('groups', function ($q) {
                    $q->where('group_id', $this->request->input('group_id'));
                });
            })
            ->get();
    }

    protected function groups(): Collection
    {
        return GroupModel::query()
            ->roleRoot()
            ->roleOwner()
            ->when($this->request->input('enterprise_id'), function ($query) {
                $query->where('enterprise_id', $this->request->input('enterprise_id'));
            })
            ->get();
    }

    private function enterprises(): Collection
    {
        if (auth()->user()->enterprise_id == null) {
            return Enterprise::query()
                ->get();
        } else {
            return Enterprise::query()
                ->where('id', auth()->user()->enterprise_id)
                ->get();
        }
    }
}
