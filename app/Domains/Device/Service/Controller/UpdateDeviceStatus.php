<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Device\Model\Device as Device;
use Illuminate\Support\Facades\Log;

class UpdateDeviceStatus extends CreateUpdateAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected Device $row)
    {
        $this->request();
    }

    public function __invoke()
    {
        return view('domains.device.update-device-status', $this->data());
    }

    public function data(): array
    {
        Log::info('Dữ liệu infoDevice:', $this->infoDevice()->toArray());

        return [
            'row' => $this->row,
            'infoDevice' => $this->infoDevice(),
            'deviceStatus' => $this->deviceStatus(),
        ];
    }

    protected function infoDevice(): \App\Domains\Device\Model\Device
    {
        return $this->cache(function () {
            $item = Device::query()
                ->where('id', $this->row->id)
                ->whenUserId($this->user()?->id)
                ->whenVehicleId($this->vehicle()?->id)
                ->withMessagesCount()
                ->withMessagesPendingCount()
                ->withUser()
                ->withVehicle()
                ->with('deviceStatus')
                ->firstOrFail();

            if (isset($item->deviceStatus->data) && is_string($item->deviceStatus->data)) {
                $decodedData = json_decode($item->deviceStatus->data, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $item->deviceStatus->data = $decodedData;
                }
            }

            return $item;
        });
    }

    protected function deviceStatus()
    {
        return $this->infoDevice()->deviceStatus ?? null;
    }
}