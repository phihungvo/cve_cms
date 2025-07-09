<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Job;

class SendEvent extends JobAbstract
{
    /**
     * @param int $id
     */
    public function __construct(protected int $id)
    {

    }

    public function middleware(): array
    {
        return [$this->middlewareWithoutOverlapping()->expireAfter(30)];
    }

    public function handle(): void
    {
        $this->factory('Cvedixrt\Event')->action(['id' => $this->id])->sendEventByJob();
    }
}
