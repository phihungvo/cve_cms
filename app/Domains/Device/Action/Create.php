<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\Device as Model;
use App\Domains\Device\Model\DeviceLog;
use App\Domains\Device\Model\DeviceStatus;

class Create extends CreateUpdateAbstract
{
    /**
     * @return void
     */
    protected function save(): void
    {
        $this->row = Model::query()->create([
            'code' => $this->data['code'],
            'name' => $this->data['name'],
            'model' => $this->data['model'],
            'serial' => $this->data['serial'],
            'phone_number' => $this->data['phone_number'],
            'password' => $this->data['password'],
            'enabled' => $this->data['enabled'],
            'shared' => $this->data['shared'],
            'shared_public' => $this->data['shared_public'],
            'vehicle_id' => $this->data['vehicle_id'],
            'user_id' => $this->data['user_id'],
            'device_type_id' => $this->data['device_type_id'],
            'enterprise_id' => $this->data['enterprise_id'],
            'camera_supported' => $this->data['camera_supported'] ?? 0,
            'camera_maximum' => $this->data['camera_maximum'] ?? 1,
            'enable_ai' => $this->data['enable_ai'],
            'instance_maximum' => $this->data['instance_maximum'],
        ]);

        $deviceStatus = DeviceStatus::query()->create([
            'serial' => $this->row->serial,
            'data' => json_encode([], JSON_FORCE_OBJECT),
            // 'error' => $this->data['error'] ?? null,
        ]);

        // Đồng bộ các nhóm thiết bị (device_group)
        $this->row->deviceGroups()->sync($this->data['device_groups']);

        // Log the creation of the device
        DeviceLog::logEvent(
            device: $this->row,
            deviceStatus: $deviceStatus,
            type: 'device_created',
            description: 'New device created with serial: '.$this->row->serial,
        );
    }
}
