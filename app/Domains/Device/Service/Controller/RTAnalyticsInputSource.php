<?php

namespace App\Domains\Device\Service\Controller;

use App\Domains\Device\Model\Device;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class RTAnalyticsInputSource extends ControllerAbstract
{
    protected Device $row;

    public function __construct(
        protected Request $request,
        protected Authenticatable $auth,
        Device $row
    ) {
        $this->row = $row;
    }

    public function data(): array
    {
        return [
            'row' => $this->row,
            'instance' => $this->instance(),
            'camerasExisting' => $this->camerasExisting(),
            'cameras' => Collect([]),
        ];
    }

    protected function camerasExisting(): Collection
    {
        return $this->row->cameras()->get();
    }

    private function instance(): array
    {
        return [
            'uuid' => $this->request->input('uuid'),
            'instance_name' => $this->request->input('instance_name'),
            'solution_id' => $this->request->input('solution_id'),
            'group_id' => $this->request->input('group_id'),
        ];
    }
}
