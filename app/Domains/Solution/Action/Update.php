<?php

namespace App\Domains\Solution\Action;

class Update extends CreateUpdateAbstract
{

    protected function save(): void
    {
        $this->row->update([
            'solution_name' => $this->data['solution_name'],
            'description' => $this->data['description'],
        ]);
    }
}
