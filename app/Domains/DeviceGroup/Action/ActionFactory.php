<?php declare(strict_types=1);

namespace App\Domains\DeviceGroup\Action;

use App\Domains\Core\Action\ActionFactoryAbstract;
use App\Domains\DeviceGroup\Model\DeviceGroupModel as Model;

class ActionFactory extends ActionFactoryAbstract
{
    protected ?Model $row;

    public function create(): Model
    {
        return $this->actionHandle(CreateAction::class, $this->validate()->create());
    }

    public function update(): Model
    {
        return $this->actionHandle(UpdateAction::class, $this->validate()->update());
    }

    /**
     * Soft Delete
     *
     * @return void
     */
    public function delete(): void
    {
        $this->actionHandle(DeleteAction::class);
    }

    /**
     * Force Delete
     *
     * @return void
     */
    public function forceDelete(): void
    {
        $this->actionHandle(ForceDeleteAction::class);
    }

    /**
     * Restore
     *
     * @return void
     */
    public function restore(): void
    {
        $this->actionHandle(RestoreAction::class);
    }

}
