<?php

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstance;

abstract class CreateUpdateInstanceAbstract extends ActionAbstract
{
    abstract protected function save();

    public function handle(?DeviceCvedixrtInstance $_instance = null):void
    {
        $this->instance = $_instance;
        $this->data();
        $this->check();
        $this->save();
    }

    protected function data(): void
    {
        $this->instanceNameData();
        $this->inputSourceData();
        $this->inputDescriptionData();
    }

    protected function check():void
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

    protected function inputDescriptionData(): void
    {
        $this->data['description'] = trim($this->data['description'] ?? '');
    }
}
