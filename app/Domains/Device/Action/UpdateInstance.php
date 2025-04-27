<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use Exception;

class UpdateInstance extends CreateUpdateInstanceAbstract
{

    /**
     * @throws Exception
     *
     * @return void
     *
     * @overide
     */
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
}

