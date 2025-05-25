<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\Device as Model;
use App\Domains\Device\Model\DeviceCvedixrtInstance;
use App\Domains\DeviceMessage\Model\DeviceMessage as DeviceMessageModel;
use App\Domains\Core\Action\ActionFactoryAbstract;

class ActionFactory extends ActionFactoryAbstract
{
    /**
     * @var ?Model
     */
    protected ?Model $row;

    protected ?DeviceCvedixrtInstance $instance;

    /**
     * @return Model
     */
    public function create(): Model
    {
        return $this->actionHandle(Create::class, $this->validate()->create());
    }

    /**
     * @return void
     */
    public function delete(): void
    {
        $this->actionHandle(Delete::class);
    }

    /**
     * @return Model
     */
    public function update(): Model
    {
        return $this->actionHandle(Update::class, $this->validate()->update());
    }

    /**
     * @return Model
     */
    public function updateBoolean(): Model
    {
        return $this->actionHandle(UpdateBoolean::class, $this->validate()->updateBoolean());
    }

    /**
     * @return \App\Domains\DeviceMessage\Model\DeviceMessage
     */
    public function updateDeviceMessageCreate(): DeviceMessageModel
    {
        return $this->actionHandle(UpdateDeviceMessageCreate::class);
    }

    /**
     * @return Model
     */
    public function updateTransfer(): Model
    {
        return $this->actionHandle(UpdateTransfer::class, $this->validate()->updateTransfer());
    }

    /**
     * @return void
     */
    public function updateCameras(): void
    {
        $this->actionHandle(UpdateCameras::class);
    }

    /**
     * @return void
     */
    public function createCamera(): void
    {
        $this->actionHandle(CreateCamera::class);
    }

    /**
     * @return void
     */
    public function deleteCamera(): void
    {
        $this->actionHandle(DeleteCamera::class);
    }

    public function createInstance(): DeviceCvedixrtInstance
    {
        return $this->actionHandle(CreateInstance::class, $this->validate()->createInstance());
    }

    public function updateInstance(DeviceCvedixrtInstance $instance): void
    {
        $this->instance = $instance;
        $this->actionHandle(UpdateInstance::class, $this->validate()->updateInstance(), $instance);
    }

    public function updateLines(DeviceCvedixrtInstance $instance): DeviceCvedixrtInstance
    {
        $this->instance = $instance;

        return $this->actionHandle(
            UpdateLines::class,
            $this->validate()->updateLines(),
            $instance
        );
    }

    public function deleteInstance(DeviceCvedixrtInstance $instance): void
    {
        $this->actionHandle(DeleteInstance::class, [], $instance);
    }
}
