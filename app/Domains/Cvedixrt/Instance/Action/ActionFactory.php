<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Action;

use App\Domains\Core\Action\ActionFactoryAbstract;
use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceModel as Model;

class ActionFactory extends ActionFactoryAbstract
{
    protected ?Model $row;

    public function create(): Model
    {
        return $this->actionHandle(CreateAction::class, $this->validate('Cvedixrt\Instance')->create());
    }

    public function update(): Model
    {
        return $this->actionHandle(UpdateAction::class, $this->validate('Cvedixrt\Instance')->update());
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
