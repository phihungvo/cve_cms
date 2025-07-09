<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Action;

use Exception;
use App\Domains\Cvedixrt\Event\Job\SendEvent;

class SendEventsAction extends ActionAbstract
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->sendEvents();
    }

    /**
     * @throws Exception
     */
    protected function sendEvents(): void
    {
        foreach ($this->data as $event) {
            SendEvent::dispatch($event['id']);
        }
    }
}
