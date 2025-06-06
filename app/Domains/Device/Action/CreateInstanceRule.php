<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstanceRule;
use Exception;
use Illuminate\Database\QueryException;

class CreateInstanceRule extends CreateUpdateInstanceRuleAbstract
{
    /**
     * @throws Exception
     *
     * @return DeviceCvedixrtInstanceRule
     *
     * @overide
     */
    protected function save(): DeviceCvedixrtInstanceRule
    {
        try {
            $this->instanceRule = DeviceCvedixrtInstanceRule::query()->create($this->data);

            return $this->instanceRule;
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'for key \'uuid\'')) {
                throw new Exception(__('rt-analytics-create.error.uuid_exists'));
            }
            throw $e;
        }
    }
}
