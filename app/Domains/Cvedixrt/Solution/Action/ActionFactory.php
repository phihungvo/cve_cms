<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Solution\Action;

use App\Domains\Core\Action\ActionFactoryAbstract;
use App\Domains\Cvedixrt\Solution\Model\CvedixrtSolutionModel as Model;

class ActionFactory extends ActionFactoryAbstract
{
    protected ?Model $row;

    public function create(): Model
    {
        return $this->actionHandle(CreateAction::class, $this->validate("Cvedixrt\Solution")->create());
    }

    public function update(): Model
    {
        return $this->actionHandle(UpdateAction::class, $this->validate("Cvedixrt\Solution")->update());
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
