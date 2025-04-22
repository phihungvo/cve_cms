<?php

namespace App\Domains\Device\Service\Controller;

use App\Domains\Device\Model\Device as Device;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class RTAnalyticsUpdate extends RTAnalyticsCreateUpdateAbstract
{

    public function __construct(
        protected Request $request,
        protected Authenticatable $auth,
        protected Device $row,
        protected $deviceCveditInstance)
    {
        $this->request();
    }

    public function data(): mixed
    {
       return $this->dataCreateUpdate() + [
            'instance' => $this->deviceCveditInstance,
               // Todo: xoa uuid sau
               'uuid' => ($this->input['uuid'] ?? helper()->uuid()),
        ];
    }
}
