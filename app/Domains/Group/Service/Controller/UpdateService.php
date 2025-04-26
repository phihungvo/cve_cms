<?php declare(strict_types=1);

namespace App\Domains\Group\Service\Controller;

use App\Domains\Device\Model\Device;
use App\Domains\Group\Model\DeviceCvedixrtGroup as Row;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{

    public function __construct(
        protected Request         $request,
        protected Authenticatable $auth,
        protected Row             $row,
        protected Device             $_device,
    ){
        $this->device = $_device;
    }

    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            'row' => $this->row,
            ];
    }
}
