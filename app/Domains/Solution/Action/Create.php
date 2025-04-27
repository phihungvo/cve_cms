<?php

namespace App\Domains\Solution\Action;

use App\Domains\Solution\Model\DeviceCvedixrtSolution as Model;

class Create extends CreateUpdateAbstract
{

    protected function save(): void
    {
        $this->row = Model::query()->create([
            'solution_name' => $this->data['solution_name'],
            'description' => $this->data['description'],
        ]);
    }
}
