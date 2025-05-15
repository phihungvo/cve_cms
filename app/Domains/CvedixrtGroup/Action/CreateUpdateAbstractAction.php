<?php declare(strict_types=1);

namespace App\Domains\CvedixrtGroup\Action;

use App\Domains\CvedixrtGroup\Model\CvedixrtGroupModel as Model;

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

    /**
     * @return void
     */
    protected function dataName(): void
    {
        $this->data['name'] = trim($this->data['name']);
    }

    /**
     * @return void
     */
    protected function dataDescription(): void
    {
        $this->data['description'] = trim($this->data['description']);
    }
}
