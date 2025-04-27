<?php

namespace App\Domains\Solution\Service\Controller;

use App\Domains\Device\Model\Device;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class CreateService extends CreateUpdateAbstractService
{

    public function __construct(protected Request $request, protected Authenticatable $auth, protected ?Device $_device)
    {
        $this->request();
        $this->device = $_device;
    }

    /**
     * Data Create Solution
     *
     * @return array
     */
    public function data(): array
    {
        return $this->dataCreateUpdate()+ [

            ];
    }
}
