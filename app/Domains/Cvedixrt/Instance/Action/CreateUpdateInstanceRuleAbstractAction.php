<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Action;

use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceRuleModel as InstanceRule;

abstract class CreateUpdateInstanceRuleAbstractAction extends ActionAbstract
{
    abstract protected function save(): InstanceRule;

    public function handle(): InstanceRule
    {
        $this->data();
        $this->check();
        $this->save();

        return $this->instanceRule;
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
        $this->data['uuid'] = $this->request->input('uuid');
    }

    protected function dataName(): void
    {
        $this->data['name'] = trim($this->request->input('name'));
    }

    protected function dataDetectedObject(): void
    {
        $this->data['detected_object'] = $this->request->input('detected_object');
    }

    protected function dataRuleType(): void
    {
        $this->data['rule_type'] = $this->request->input('rule_type');
    }

    protected function dataDrawingObject(): void
    {
        $this->data['drawing_object'] = $this->request->input('drawing_object');
    }

    protected function dataDirection(): void
    {
        $this->data['direction'] = $this->request->input('direction');
    }

    protected function dataInstanceId(): void
    {
        $this->data['cvedixrt_instance_id'] = $this->row?->id;
    }

}
