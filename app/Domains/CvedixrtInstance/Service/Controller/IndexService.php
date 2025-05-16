<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Service\Controller;

use App\Domains\CvedixrtGroup\Model\CvedixrtGroupModel;
use App\Domains\CvedixrtInstance\Model\CvedixrtInstance as Model;
use App\Domains\CvedixrtSolution\Model\CvedixrtSolutionModel;
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

    public function data(): mixed
    {
        return [
            'list' => $this->list(),
            'solutions' => $this->solutions(),
            'groups' => $this->groups(),
        ];
    }

    protected function list(): Collection
    {
        return Model::query()
            ->whereBySolution((int)$this->request->input('cvedixrt_solution_id'))
            ->whereByGroup((int)$this->request->input('cvedixrt_group_id'))
            ->with(['group:id,name', 'solution:id,name'])
            ->get();
    }

    protected function solutions(): Collection
    {
        return CvedixrtSolutionModel::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }

    protected function groups(): Collection
    {
        return CvedixrtGroupModel::query()
            ->select(['id', 'name'])
            ->orderBy('name')
            ->get();
    }
}
