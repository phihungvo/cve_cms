<?php declare(strict_types=1);

namespace App\Domains\User\Permission\Action;

use App\Domains\User\Permission\Model\Permission as Model;
use App\Domains\Core\Action\ActionFactoryAbstract;

class ActionFactory extends ActionFactoryAbstract
{
    protected ?Model $row;

    public function create(array $data): Model
    {
        return $this->actionHandle(Create::class, $data, $data);
    }

    public function update(Model $feature, array $data): Model
    {
        $this->row = $feature;
        return $this->actionHandle(Update::class, $data, $data);
    }

    public function delete(Model $feature): void
    {
        $this->row = $feature;
        $this->actionHandle(Delete::class);
    }
}