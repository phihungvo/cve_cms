<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCveditInstance;

class DeleteInstance extends ActionAbstract
{
    protected ?DeviceCveditInstance $instance;
    public function handle()
    {
        $this->data();
        $this->delete();
    }

    protected function delete()
    {
        $this->instance
            ->delete();
    }

    protected function data()
    {
        $this->getInstance();
    }

    protected function getInstance()
    {
        $this->instance = DeviceCveditInstance::query()
            ->where('device_id', $this->row->id)
            ->first();

        if (!$this->instance) {
            throw new \Exception('Instance not found');
        }
    }
}
