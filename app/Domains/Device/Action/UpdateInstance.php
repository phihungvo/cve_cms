<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstance;
use Exception;

class UpdateInstance extends CreateUpdateInstanceAbstract
{
    /**
     * @throws Exception
     *
     * @return DeviceCvedixrtInstance
     *
     * @overide
     */
    protected function save(): DeviceCvedixrtInstance
    {
        $this->instance->update([
            'uuid' => $this->data['uuid'],
            'instance_name' => $this->data['instance_name'],
            'solution_id' => $this->data['solution_id'],
            'group_id' => $this->data['group_id'],
            'input_source' => $this->data['input_source'],
            'description' => $this->data['description'],
        ]);
        return $this->instance;
    }
}
