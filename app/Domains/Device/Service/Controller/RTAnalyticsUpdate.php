<?php

namespace App\Domains\Device\Service\Controller;

use App\Domains\Device\Model\Device;
use App\Domains\Device\Model\DeviceCvedixrtInstance;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class RTAnalyticsUpdate extends RTAnalyticsCreateUpdateAbstract
{
    public function __construct(
        protected Request $request,
        protected Authenticatable $auth,
        protected Device $row,
        protected DeviceCvedixrtInstance $deviceCvedixrtInstance
    ) {
        $this->request();
    }

    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            'instance' => $this->deviceCvedixrtInstance,
            'camerasExisting' => $this->camerasExisting(),
        ];
    }

    protected function camerasExisting(): Collection
    {
        return $this->row->cameras()->get();
    }
}
