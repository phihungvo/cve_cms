<?php declare(strict_types=1);

namespace App\Domains\Vehicle\Service\Controller;

use App\Domains\VehicleGroup\Model\VehicleGroupModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Domains\Timezone\Model\Timezone as TimezoneModel;

class Create extends CreateUpdateAbstract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->request();
    }

    /**
     * @return void
     */
    protected function request(): void
    {
        $this->requestMergeWithRow([
            'user_id' => $this->user()->id,
            'timezone_id' => $this->timezoneId(),
        ]);
    }

    /**
     * @return int
     */
    protected function timezoneId(): int
    {
        return $this->cache(
            fn () => TimezoneModel::query()
                ->whereDefault()
                ->value('id')
        );
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            'bookmarkGroups' => $this->bookmarkGroups(),
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
                return Collection::make([]);
            }

        } else {
            // role owner
            return VehicleGroupModel::query()
                ->where('enterprise_id', $this->auth->enterprise->id)->get();
        }
    }
}
