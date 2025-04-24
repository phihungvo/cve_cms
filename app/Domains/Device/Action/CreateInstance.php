<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstance;

class CreateInstance extends ActionAbstract
{
    public function handle(): void
    {
        $this->data();
        $this->check();
        $this->save();
    }

    protected function data()
    {
        $this->instanceNameData();
        $this->inputSourceData();
        $this->inputDescriptionData();
    }

    protected function check()
    {
    }

    protected function save()
    {
        DeviceCvedixrtInstance::query()->create([
            'uuid' => $this->data['uuid'],
            'instance_name' => $this->data['instance_name'],
            'device_id' => $this->row->id,
            'solution_id' => $this->data['solution_id'],
            'group_id' => $this->data['group_id'],
            'input_source' => $this->data['input_source'] ?? null,
            'description' => $this->data['description'] ?? null,
        ]);
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

    protected function inputDescriptionData(): void
    {
        $this->data['description'] = trim($this->data['description'] ?? '');
    }
}
