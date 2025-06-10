<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use App\Domains\Device\Model\Device as Model;

use App\Domains\Device\Model\DeviceCvedixrtInstance;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RTAnalyticsIndex extends RTAnalyticsCreateUpdateAbstract
{

    public function __construct(protected Request $request, protected Authenticatable $auth, protected Model $row)
    {
        $this->request();
    }

    public function data(): array
    {
        return [
            'row' => $this->row,
            'list' => $this->list(),
            'solutions' => $this->solutions(),
            'groups' => $this->groups(),
        ];
    }

    protected function list(): Collection
    {
        return DeviceCvedixrtInstance::query()
            ->whereByDevice($this->row->id)
            ->whereBySolution((int)$this->request->input('solution_id'))
            ->whereByGroup((int)$this->request->input('group_id'))
            ->with(['group:id,group_name', 'solution:id,solution_name'])
            ->get();
    }
}
