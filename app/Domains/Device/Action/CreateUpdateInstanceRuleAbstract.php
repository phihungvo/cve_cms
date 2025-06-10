<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstance;
use App\Domains\Device\Model\DeviceCvedixrtInstanceRule;

abstract class CreateUpdateInstanceRuleAbstract extends ActionAbstract
{
    abstract protected function save(): DeviceCvedixrtInstanceRule;

    public function handle(?DeviceCvedixrtInstanceRule $_instanceRule = null): DeviceCvedixrtInstanceRule
    {
        $this->instanceRule = $_instanceRule;
        $this->data();
        $this->check();

        return $this->save();
    }

    protected function data(): void
    {
        if ($this->instanceRule === null) { // Nếu đang tạo mới
            $this->dataUuid();
        }
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
        $this->data['uuid'] = helper()->uuid();
    }

    protected function dataName(): void
    {
        $this->data['name'] = trim($this->request->input('name'));
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

    protected function dataRuleType()
    {
    }
}
