<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Action;

use App\Domains\CvedixrtInstance\Model\CvedixrtInstanceModel as Model;

abstract class CreateUpdateAbstractAction extends ActionAbstract
{
    abstract protected function save():Model;

    public function handle(): Model
    {
        $this->data();
        $this->check();
        $this->save();

        return $this->row;
    }

    protected function data(): void
    {
       // TODO: implement logic data
    }

    protected function check(): void
    {
        // TODO: implement logic here

    }
}
