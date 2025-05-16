<?php declare(strict_types=1);

namespace App\Domains\CvedixrtSolution\Action;

use App\Domains\CvedixrtSolution\Model\CvedixrtSolutionModel as Model;

abstract class CreateUpdateAbstractAction extends ActionAbstract
{
    abstract protected function save(): Model;

    public function handle(): Model
    {
        $this->data();
        $this->check();
        $this->save();

        return $this->row;
    }

    protected function data(): void
    {
        $this->dataName();
        $this->dataDescription();
    }

    protected function check(): void
    {

    }

    private function dataName(): void
    {
        $this->data['name'] = trim($this->data['name']);
    }

    private function dataDescription(): void
    {
        $this->data['description'] = trim($this->data['description']);
    }
}
