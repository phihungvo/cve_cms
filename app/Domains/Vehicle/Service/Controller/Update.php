<?php declare(strict_types=1);

namespace App\Domains\Vehicle\Service\Controller;

use App\Domains\VehicleGroup\Model\VehicleGroupMap;
use App\Domains\VehicleGroup\Model\VehicleGroupModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Domains\Vehicle\Model\Vehicle as Model;

class Update extends CreateUpdateAbstract
{
    /**
     * @param Request $request
     * @param Authenticatable $auth
     * @param Model $row
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth, protected Model $row)
    {
        $this->request();
    }

    /**
     * @return void
     */
    protected function request(): void
    {
        $this->requestMergeWithRow();
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            'row' => $this->row,
            'bookmarkGroups' => $this->bookmarkGroups(),
            'assignedBookmarkGroups' => $this->assignedBookmarkGroups(),
        ];
    }

    protected function bookmarkGroups(): Collection
    {
        if ($this->auth->enterprise_id == null) {
            // role root
            if ($this->request->input('enterprise_id')) {
                return VehicleGroupModel::query()
                    ->where('enterprise_id', $this->request->input('enterprise_id'))
                    ->get();
            } else {
                return VehicleGroupModel::query()->get();
            }

        } else {
            // role owner
            return VehicleGroupMap::query()
                ->where('enterprise_id', $this->auth->enterprise->id)->get();
        }
    }

    protected function assignedBookmarkGroups(): Collection
    {
        return VehicleGroupMap::query()
            ->where('vehicle_id', $this->row->id)
            ->get();
    }
}
