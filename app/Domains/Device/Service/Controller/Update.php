<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use App\Domains\DeviceGroup\Model\DeviceGroupMap;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use App\Domains\Device\Model\Device;
use Illuminate\Support\Facades\Log;
use App\Domains\Device\Model\DeviceLog;
use App\Domains\User\Enterprise\Model\Enterprise;

class Update extends CreateUpdateAbstract
{
    /**
     * @param Request $request
     * @param Authenticatable $auth
     * @param Device $row
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth, protected Device $row)
    {
        $this->request();
    }

    /**
     * @return array
     */
    public function data(): array
    {
        $data = $this->dataCreateUpdate() + [
            'row' => $this->row,
            'assignedDeviceGroups' => $this->assignedDeviceGroups(),
            'infoDevice' => $this->infoDevice(),
            'deviceLogs' => $this->deviceLogs(),
        ];

        // Thêm dữ liệu enterprise
        if ($this->auth->isRoot()) {
            $data['enterprises'] = Enterprise::all()->map(fn ($e) => [
                'id' => $e->id,
                'name' => $e->name,
            ])->toArray();
        } else {
            $data['enterprise_name'] = $this->row->enterprise->name ?? 'N/A'; // Lấy từ thiết bị hiện tại
            $data['enterprise_id'] = $this->row->enterprise_id ?? null;
        }

        Log::info('Dữ liệu infoDevice:', $this->infoDevice()->toArray());

        return $data;
    }

    protected function infoDevice(): Device
    {
        $deviceId = $this->request->route('id');

        return $this->cache(function () use ($deviceId) {
            $item = Device::query()
                ->where('id', $deviceId)
                // ->whenUserId($this->user()?->id)
                // ->whenVehicleId($this->vehicle()?->id)
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

            return $item; // Trả về trực tiếp model instance thay vì Collection
        });
    }

    /**
     * @return Collection
     */
    protected function deviceLogs(): Collection
    {
        return DeviceLog::query()
            ->where('serial', $this->row->serial)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    protected function assignedDeviceGroups(): Collection
    {
        return DeviceGroupMap::query()
            ->where('device_id', $this->row->id)
            ->get();
    }
}
