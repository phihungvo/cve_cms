<?php declare(strict_types=1);

namespace App\Domains\Device\Service\ControllerApi;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Device\Model\DeviceStatus as DeviceStatusModel;

class GetDeviceStatusBySerial extends ControllerApiAbstract
{
    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     * @param string|null $serial
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth, protected ?string $serial)
    {
    }

    /**
     * @return array
     */
    public function data(): array
    {
        if (!$this->serial) {
            return [];
        }

        $deviceStatus = DeviceStatusModel::query()
            ->where('serial', $this->serial)
            ->first();

        return $deviceStatus && $deviceStatus->data ? (array) $deviceStatus->data : [];
    }
}