<?php

namespace App\Domains\Solution\Action;

use App\Domains\Solution\Model\DeviceCvedixrtSolution as Model;
use App\Exceptions\ValidatorException;

abstract class CreateUpdateAbstract extends ActionAbstract
{
    abstract protected function save(): void;

    /**
     * @throws ValidatorException
     */
    public function handle(): void
    {
        $this->data();
        $this->check();
        $this->save();
    }

    protected function data(): void
    {
        $this->dataSolutionName();
        $this->dataDescription();
    }

    /**
     * @throws ValidatorException
     */
    protected function check(): void
    {
        $this->existSolutionName();
    }

    private function dataSolutionName(): void
    {
        $this->data['solution_name'] = trim($this->data['solution_name']);
    }

    private function dataDescription(): void
    {
        $this->data['description'] = trim($this->data['description']);
    }

    /**
     * @throws ValidatorException
     */
    protected function existSolutionName(): void
    {
        if (Model::query()
            ->where('solution_name', $this->data['solution_name'])
            ->exists()) {
            throw new ValidatorException(__('solution-create.error.solution_name-exists', [
                'solution_name' => $this->data['solution_name']
            ]));
        }
    }
}
