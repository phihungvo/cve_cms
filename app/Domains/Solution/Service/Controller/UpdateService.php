<?php

namespace App\Domains\Solution\Service\Controller;

use App\Domains\Device\Model\Device;
use App\Domains\Solution\Model\DeviceCvedixrtSolution as Row;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{

    public function __construct(
        protected Request         $request,
        protected Authenticatable $auth,
        protected Row             $row,
        protected ?Device $_device
    )
    {
        $this->request();
        $this->device = $_device;
    }


    public function data(): array
    {
        return $this->dataCreateUpdate() + [
                'row' => $this->row,
            ];
    }
}
