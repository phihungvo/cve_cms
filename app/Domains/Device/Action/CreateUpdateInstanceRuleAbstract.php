<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstance;

abstract class CreateUpdateInstanceRuleAbstract extends ActionAbstract
{
    abstract protected function save();

    public function handle(?DeviceCvedixrtInstance $_instance = null): DeviceCvedixrtInstance
    {
        $this->instance = $_instance;
        $this->data();
        $this->check();

        return $this->save();
    }

    protected function data(): void
    {
        $this->dataUuid();
        $this->dataName();
        $this->dataDetectedObject();
        $this->dataRuleType();
        $this->dataDrawingObject();
        $this->dataDirection();
        $this->dataInstanceId();
    }

    protected function check(): void
    {
    }

    protected function dataUuid(): void
    {
    }

    protected function dataName(): void
    {
    }

    protected function dataDetectedObject()
    {
    }

    protected function dataDrawingObject()
    {
    }

    protected function dataDirection()
    {
    }

    protected function dataInstanceId()
    {
    }
}
