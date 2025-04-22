<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCveditInstance;

class CreateInstance extends ActionAbstract
{
    public function handle()
    {
        $this->data();
        $this->check();
        $this->save();
    }

    protected function data()
    {
    }

    protected function check()
    {
    }

    protected function save()
    {
        DeviceCveditInstance::query()->create([
            'uuid' => $this->data['uuid'],
            'instance_name' => $this->data['instance_name'],
            'device_id' => $this->row->id,
            'solution_id' => $this->data['solution_id'],
            'group_id' => $this->data['group_id'],
        ]);
    }
}
