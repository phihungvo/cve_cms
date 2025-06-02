<?php declare(strict_types=1);

namespace App\Domains\DeviceGroup\Service\Controller;

use App\Domains\DeviceGroup\Model\DeviceGroupModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected DeviceGroupModel $row)
    {
        $this->request();
    }

    /**
     * Data update DeviceGroup
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
