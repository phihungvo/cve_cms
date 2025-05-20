<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Service\Controller;

use App\Domains\CamCloud\Model\Camera;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class CreateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->request();
    }

    /**
     * Data for creating CvedixrtInstance
     *
     * @return array
     */
    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            'uuid' => ($this->input['uuid'] ?? helper()->uuid()),
        ];
    }

    public function dataInputSource(): array
    {
        return [
            'instance' => $this->instance(),
            'camerasExisting' => $this->camerasExisting(),
            'cameras' => Collect([]),
        ];
    }

    protected function instance(): array
    {
        return [
            'uuid' => $this->request->input('uuid'),
            'name' => $this->request->input('instance_name'),
            'solution_id' => $this->request->input('solution_id'),
            'group_id' => $this->request->input('group_id'),
        ];
    }

    protected function camerasExisting(): Collection
    {
        return Camera::query()
            ->get();
    }
}
