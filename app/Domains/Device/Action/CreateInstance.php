<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstance;
use Exception;
use Illuminate\Database\QueryException;

class CreateInstance extends CreateUpdateInstanceAbstract
{
    /**
     * @return DeviceCvedixrtInstance
     *
     * @throws Exception
     * @overide
     */
    protected function save(): DeviceCvedixrtInstance
    {
        try {
         return  DeviceCvedixrtInstance::query()->create([
                'uuid' => $this->data['uuid'],
                'instance_name' => $this->data['instance_name'],
                'device_id' => $this->row->id,
                'solution_id' => $this->data['solution_id'],
                'group_id' => $this->data['group_id'],
                'input_source' => $this->data['input_source'] ?? null,
                'description' => $this->data['description'] ?? null,
            ]);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'for key \'uuid\'')) {
                throw new Exception(__('rt-analytics-create.error.uuid_exists'));
            }
            throw $e;
        }
    }
}

