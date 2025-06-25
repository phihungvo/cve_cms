<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Action;

use App\Domains\Core\Action\ActionFactoryAbstract;
use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel as Model;
use App\Domains\Cvedixrt\Event\Service\Controller\CreateAction;

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

    public function sendEvents($data): void
    {
        $this->actionHandle(SendEventsAction::class, $data);
    }

    public function sendSingleEvent($dataSingle): void
    {
        $this->actionHandle(SendSingleEventAction::class, $dataSingle);
    }

    public function sendEventByJob(): void
    {
        $this->actionHandle(SendEventByJob::class, $this->data);
    }
}
