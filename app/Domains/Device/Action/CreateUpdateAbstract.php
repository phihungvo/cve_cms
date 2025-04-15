<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\Device as Model;
use App\Domains\Vehicle\Model\Vehicle as VehicleModel;
use App\Domains\Device\Model\Camera;

abstract class CreateUpdateAbstract extends ActionAbstract
{
    /**
     * @return void
     */
    abstract protected function save(): void;

    /**
     * @return \App\Domains\Device\Model\Device
     */
    public function handle(): Model
    {
        $this->data();
        $this->check();
        $this->save();

        return $this->row;
    }

    /**
     * @return void
     */
    protected function data(): void
    {
        $this->dataName();
        $this->dataModel();
        $this->dataSerial();
        $this->dataPassword();
        $this->dataUserId();
        $this->dataDeviceTypeId();
        $this->dataEnterpriseId();
        $this->dataCameraSupported();
        $this->dataCameraMaximum();
    }

    /**
     * @return void
     */
    protected function dataName(): void
    {
        $this->data['name'] = trim($this->data['name']);
    }

    /**
     * @return void
     */
    protected function dataModel(): void
    {
        $this->data['model'] = trim($this->data['model']);
    }

    /**
     * @return void
     */
    protected function dataSerial(): void
    {
        $this->data['serial'] = trim($this->data['serial']);
    }

    /**
     * @return void
     */
    protected function dataPassword(): void
    {
        if (empty($this->data['password'])) {
            $this->data['password'] = $this->row->password ?? '';
        }
    }
    // protected function dataEnterpriseId(): void
    // {
    //     if ($this->auth->isRoot()) {
    //         $this->data['enterprise_id'] = $this->request->input('enterprise_id');
    //     } else {
    //         $this->data['enterprise_id'] = $this->auth->enterprise_id;
    //     }
    // }
    protected function dataEnterpriseId(): void
    {
        $this->data['enterprise_id'] = $this->request->input('enterprise_id');
    }
    protected function dataCameraSupported(): void
    {
        $this->data['camera_supported'] = (bool) $this->request->input('camera_supported', $this->row->camera_supported ?? 0);
    }

    protected function dataCameraMaximum(): void
    {
        $this->data['camera_maximum'] = (int) $this->request->input('camera_maximum', $this->row->camera_maximum ?? 1);
    }

    /**
     * @return void
     */
    protected function check(): void
    {
        $this->checkCode();
        $this->checkSerial();
        $this->checkVehicleId();
        // $this->checkVehicleId();
    }

    /**
     * @return void
     */
    protected function checkCode(): void
    {
        if ($this->checkCodeExists()) {
            $this->exceptionValidator(__('device-create.error.code-exists'));
        }
    }

    /**
     * @return bool
     */
    protected function checkCodeExists(): bool
    {
        return Model::query()
            ->byIdNot($this->row->id ?? 0)
            ->byCode($this->data['code'])
            ->exists();
    }

    /**
     * @return void
     */
    protected function checkSerial(): void
    {
        if ($this->checkSerialExists()) {
            $this->exceptionValidator(__('device-create.error.serial-exists'));
        }
    }

    /**
     * @return bool
     */
    protected function checkSerialExists(): bool
    {
        return Model::query()
            ->byIdNot($this->row->id ?? 0)
            ->bySerial($this->data['serial'])
            ->exists();
    }

    /**
     * @return void
     */
    protected function checkVehicleId(): void
    {
        if ($this->data['vehicle_id'] && ($this->checkVehicleIdExists() === false)) {
            $this->exceptionValidator(__('device-create.error.vehicle-exists'));
        }
    }

    /**
     * @return bool
     */
    protected function checkVehicleIdExists(): bool
    {
        return VehicleModel::query()
            ->byId($this->data['vehicle_id'])
            ->byUserId($this->data['user_id'])
            ->exists();
    }

    /**
     * @return void
     */
    protected function dataDeviceTypeId(): void
    {
        $this->data['device_type_id'] = $this->request->input('device_type_id');
    }

}