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
        // logic xử lý data
    }

    protected function check(): void
    {
    }
}
