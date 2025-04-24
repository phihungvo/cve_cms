<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstance;
use Exception;

class UpdateInstance extends ActionAbstract
{
    protected ?DeviceCvedixrtInstance $instance;

    /**
     * @throws Exception
     */
    public function handle($instance): void
    {
        $this->instance = $instance;
        $this->data();
        $this->check();
        $this->save();
    }

    /**
     * @throws Exception
     */
    protected function data(): void
    {
        $this->uuid();
        $this->instanceNameData();
        $this->inputSourceData();
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
            'solution_id' => $this->data['solution_id'],
            'group_id' => $this->data['group_id'],
            'input_source' => $this->data['input_source'],
            'description' => $this->data['description'],
        ]);
    }

    protected function uuid()
    {

    }

    protected function instanceNameData(): void
    {
        $this->data['instance_name'] = trim($this->data['instance_name']);
    }

    protected function inputSourceData(): void
    {
        if (isset($this->data['uri'])) {
            $this->data['input_source'] = $this->data['uri'];
        } else if (isset($this->data['input_source_text'])) {
            $this->data['input_source'] = $this->data['input_source_text'];
        } else {
            $this->data['input_source'] = null;
        }
    }
}
