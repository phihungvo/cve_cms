<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Action;

use App\Domains\ScheduleGroup\Model\ScheduleGroupModel as Model;

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
        $this->dataEnterpriseId();
    }

    protected function check(): void
    {
    }

    protected function dataName(): void
    {
        $this->data['name'] = trim($this->data['name']);
    }

    protected function dataDescription(): void
    {
        $this->data['description'] = trim($this->data['description']);
    }

    protected function dataEnterpriseId(): void
    {
        if (auth()->user()->enterprise_id == null) {
            $this->data['enterprise_id'] ??= null;
        } else {
            $this->data['enterprise_id'] = auth()->user()->enterprise_id;
        }
    }
}
