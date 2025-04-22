<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCveditInstance;

class UpdateInstance extends ActionAbstract
{
    protected ?DeviceCveditInstance $instance;

    /**
     * @throws \Exception
     */
    public function handle(): void
    {
        $this->data();
        $this->check();
        $this->save();
    }

    /**
     * @throws \Exception
     */
    protected function data(): void
    {
        $this->getInstance();
        $this->uuid();
    }

    /**
     * @return void
     */
    protected function check():void
    {
    }


    protected function save(): void
    {
        $this->instance->update([
            'uuid' => $this->data['uuid'],
            'instance_name' => $this->data['instance_name'],
            'instance_source' => $this->data['instance_source'],
            'solution_id' => $this->data['solution_id'],
            'group_id' => $this->data['group_id'],
        ]);
    }

    /**
     * @throws \Exception
     */
    protected function getInstance(): void
    {
        $this->instance = DeviceCveditInstance::query()
            ->where('device_id', $this->row->id)
            ->first();

        if (!$this->instance) {
            throw new \Exception('Instance not found');
        }
    }

    protected function uuid()
    {

    }

}
