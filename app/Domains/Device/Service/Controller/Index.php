<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\Device\Model\Collection\Device as Collection;
use App\Domains\Device\Model\Device as Device;
use App\Domains\Device\Model\DeviceType as DeviceType;
use App\Domains\Device\Model\DeviceStatus as DeviceStatus;
use Illuminate\Support\Facades\Log;

class Index extends ControllerAbstract
{
    /**
     * @var bool
     */
    protected bool $userEmpty = true;

    /**
     * @var bool
     */
    protected bool $vehicleEmpty = true;

    /**
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Contracts\Auth\Authenticatable $auth
     *
     * @return self
     */
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->filters();
    }

    /**
     * @return void
     */
    protected function filters(): void
    {
        $this->filtersUserId();
        $this->filtersVehicleId();
        $this->filtersDeviceType();
    }

    /**
     * @return void
     */
    protected function filtersDeviceType(): void
    {
        $this->request->merge(['device_type' => $this->filtersId('device_type', $this->vehicleEmpty)]);
    }

    /**
     * @return array
     */
    public function data(): array
    {
        return $this->dataCore() + [
            'vehicles' => $this->vehicles(),
            'vehicles_multiple' => $this->vehiclesMultiple(),
            'vehicle' => $this->vehicle(),
            'vehicle_empty' => $this->vehicleEmpty(),
            'list' => $this->list(),
            'device_type' => $this->getAllDeviceTypes(),
            'users' => $this->users(),
            'users_multiple' => $this->usersMultiple(),
            'user_empty' => $this->userEmpty,
        ];
    }

    /**
     * @return \App\Domains\Device\Model\Collection\Device
     */
    protected function list(): Collection
    {
        return $this->cache(function () {
            $query = Device::query()
                ->withMessagesCount()
                ->withMessagesPendingCount()
                ->withUser()
                ->withVehicle()
                ->with('deviceStatus')
                ->with('deviceType'); // Thêm quan hệ deviceType

            // Filter theo user_id
            if ($user_id = $this->request->input('user_id')) {
                $query->where('user_id', $user_id);
            }

            // Filter theo vehicle_id
            if ($vehicle_id = $this->request->input('vehicle_id')) {
                $query->where('vehicle_id', $vehicle_id);
            }

            // Filter theo device_type
            if ($device_type = $this->request->input('device_type')) {
                $query->whereHas('deviceType', function ($q) use ($device_type) {
                    $q->where('name', $device_type); // Lọc theo tên device_type
                });
            }

            // Filter theo enterprise nếu không phải root
            if (!$this->auth->isRoot()) {
                $query->byEnterpriseId($this->auth->enterprise->id);
            }

            $items = $query->get()->map(function ($item) {
                if (isset($item->deviceStatus->data) && is_string($item->deviceStatus->data)) {
                    $decodedData = json_decode($item->deviceStatus->data, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $item->deviceStatus->data = $decodedData;
                        if (isset($decodedData['updated_at'])) {
                            $item->deviceStatus->updated_at = $decodedData['updated_at'];
                        }
                    }
                }
                return $item;
            })->values();

            return $items;
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getAllDeviceTypes()
    {
        return DeviceType::all();
    }
}
