<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\Device as Model;
use App\Domains\Device\Model\DeviceType;
use App\Domains\Vehicle\Model\Vehicle;
use App\Domains\Vehicle\Model\Vehicle as VehicleModel;
use App\Exceptions\ValidatorException;

abstract class CreateUpdateAbstract extends ActionAbstract
{
    /**
     * @return void
     */
    abstract protected function save(): void;

    /**
     * @return Model
     * @throws ValidatorException
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

    protected function dataUserId(): void
    {
        $this->data['user_id'] = $this->data['vehicle_id'] ? Vehicle::find($this->data['vehicle_id'])->user_id : null;
    }

    protected function dataEnterpriseId(): void
    {
        $enterpriseId = null;
        if($this->auth->isRoot()) {
            if ($this->request->input('enterprise_id')) {
                $enterpriseId = $this->request->input('enterprise_id');
            }
        } else {
            $enterpriseId = $this->auth->enterprise_id;
        }

        $this->data['enterprise_id'] = $enterpriseId;
    }

    protected function dataCameraSupported(): void
    {
        $this->data['camera_supported'] = (bool)$this->request->input('camera_supported', $this->row->camera_supported ?? 0);
    }

    protected function dataCameraMaximum(): void
    {
        $this->data['camera_maximum'] = (int)$this->request->input('camera_maximum', $this->row->camera_maximum ?? 1);
        if (!$this->data['camera_supported']) {
            $this->data['camera_maximum'] = 0;
        }
    }

    /**
     * @return void
     * @throws ValidatorException
     */
    protected function check(): void
    {
        $this->checkCode();
        $this->checkSerial();
        $this->checkVehicleId();
    }

    /**
     * @return void
     * @throws ValidatorException
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
     * @throws ValidatorException
     */
    protected function checkSerial(): void
    {
        if ($this->checkSerialExists()) {
            throw new ValidatorException(__('device-create.error.serial-exists', ['serial' => $this->data['serial']]));
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
     * @throws ValidatorException
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
            ->exists();
    }

    /**
     * @return void
     * @throws ValidatorException
     */
    protected function dataDeviceTypeId(): void
    {
        $this->data['device_type_id'] = $this->request->input('device_type_id');
        if ($this->data['device_type_id'] && ($this->checkDeviceTypeIdExists() === false)) {
            $this->exceptionValidator(__('device-create.error.vehicle-not-found'));
        }
    }

    protected function checkDeviceTypeIdExists(): bool
    {
        return DeviceType::query()
            ->where('id', $this->data['device_type_id'])
            ->exists();
    }
}
