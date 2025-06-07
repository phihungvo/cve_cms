<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstance;

class DeleteInstance extends ActionAbstract
{
    public function handle(?DeviceCvedixrtInstance $instance): void
    {
        $this->instance = $instance;
        $this->delete();
    }

    protected function delete(): void
    {
        $this->instance->delete();

        // try catch
    }
}
