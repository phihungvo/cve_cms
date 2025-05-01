<?php

namespace App\Domains\Solution\Action;
use App\Domains\Core\Action\ActionFactoryAbstract;
use App\Domains\Solution\Model\DeviceCvedixrtSolution as Model;

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

    public function delete()
    {
        $this->actionHandle(Delete::class);
    }

}
