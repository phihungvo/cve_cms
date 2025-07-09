<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Action;

use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel as Model;

abstract class CreateUpdateAbstractAction extends ActionAbstract
{
    abstract protected function save(): void;

    public function handle(): Model
    {
        $this->data();
        $this->check();
        $this->save();

        return $this->row;
    }

    protected function data(): void
    {
    }

    protected function check(): void
    {
    }
}
