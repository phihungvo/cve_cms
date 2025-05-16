<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Action;

use App\Domains\CvedixrtInstance\Model\CvedixrtInstanceModel as Model;

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
        $this->nameData();
        $this->dataInputSourceData();
        $this->descriptionData();
    }

    protected function check(): void
    {
    }

    protected function nameData(): void
    {
        $this->data['name'] = trim($this->request->input('name'));
    }

    protected function dataInputSourceData(): void
    {
        if (isset($this->data['uri'])) {
            $this->data['source'] = $this->data['uri'];
            unset($this->data['uri']);
            unset($this->data['input_source_text']);
        } elseif (isset($this->data['input_source_text'])) {
            $this->data['source'] = $this->data['input_source_text'];
            unset($this->data['input_source_text']);
            unset($this->data['uri']);
        } else {
            $this->data['source'] = null;
            unset($this->data['input_source_text']);
            unset($this->data['uri']);
        }
    }

    protected function descriptionData(): void
    {
        $this->data['description'] = trim($this->request->input('description') ?? '');
    }
}
