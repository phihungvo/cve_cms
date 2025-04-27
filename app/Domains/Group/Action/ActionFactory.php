<?php declare(strict_types=1);

namespace App\Domains\Group\Action;

use App\Domains\Core\Action\ActionFactoryAbstract;
use App\Domains\Group\Model\DeviceCvedixrtGroup as Model;

class ActionFactory extends ActionFactoryAbstract
{
    protected ?Model $row;

    public function create()
    {
        return $this->actionHandle(Create::class, $this->validate()->create());
    }

    public function update()
    {
        return $this->actionHandle(Update::class, $this->validate()->update());
    }

    public function delete(): void
    {
        $this->actionHandle(Delete::class);
    }
}
