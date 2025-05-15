<?php declare(strict_types=1);

namespace App\Domains\CvedixGroup\Action;

use App\Domains\Core\Action\ActionFactoryAbstract;
use App\Domains\CvedixGroup\Model\CvedixGroupModel as Model;

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

    
}
