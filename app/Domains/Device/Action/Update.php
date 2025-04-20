<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

class Update extends CreateUpdateAbstract
{
    /**
     * @return void
     */
    protected function save(): void
    {
        $this->row->user_id = $this->data['user_id'] ?? null;
        $this->row->enterprise_id = $this->data['enterprise_id'];
        $this->row->code = $this->data['code'];
        $this->row->name = $this->data['name'];
        $this->row->model = is_string($this->data['model']) ? $this->data['model'] : null;
        $this->row->serial = $this->data['serial'];
        $this->row->phone_number = $this->data['phone_number'];
        $this->row->password = $this->data['password'];
        $this->row->enabled = $this->data['enabled'];
        $this->row->shared = $this->data['shared'];
        $this->row->shared_public = $this->data['shared_public'];
        $this->row->vehicle_id = $this->data['vehicle_id'];
        $this->row->device_type_id = $this->data['device_type_id'];

        if ($this->auth->isRoot()) {
            $this->row->enterprise_id = $this->data['enterprise_id']; // Root được phép thay đổi
        }

        $previousCameraSupported = $this->row->camera_supported;
        $this->row->camera_supported = $this->data['camera_supported'] ?? 0;
        $this->row->camera_maximum = $this->data['camera_maximum'] ?? 1;

        $this->row->save();

        // If camera_supported is now disabled and was previously enabled, delete cameras
        if (!$this->row->camera_supported && $previousCameraSupported) {
            $this->row->cameras()->delete();
        }
    }

}
