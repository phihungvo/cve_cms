<?php declare(strict_types=1);

namespace App\Domains\VehicleGroup\Service\Controller;

use App\Domains\VehicleGroup\Model\VehicleGroupModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected VehicleGroupModel $row)
    {
        $this->request();
    }

    /**
     * Data update VehicleGroup
     *
     * @return array
     */
    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            'row' => $this->row,
            // and more ...
        ];
    }
}
