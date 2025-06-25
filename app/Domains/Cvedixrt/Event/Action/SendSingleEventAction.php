<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Action;

use App\Domains\Cvedixrt\Event\Job\SendEvent;
use Exception;

class SendSingleEventAction extends ActionAbstract
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->sendSingleEvents();
    }

    /**
     * @throws Exception
     */
    protected function sendSingleEvents(): void
    {
        foreach ($this->data as $data) {
            SendEvent::dispatch($data['id']);
        }
    }
}
