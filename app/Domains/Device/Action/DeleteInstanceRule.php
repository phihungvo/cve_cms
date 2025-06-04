<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstanceRule;

class DeleteInstanceRule extends ActionAbstract
{

    public function handle(?DeviceCvedixrtInstanceRule $instanceRule): void
    {
        $this->instanceRule = $instanceRule;
        $this->delete();
    }

    protected function delete(): void
    {
        $this->instanceRule->delete();
    }
}
