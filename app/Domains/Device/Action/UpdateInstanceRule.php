<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstanceRule;
use Exception;

class UpdateInstanceRule extends CreateUpdateInstanceRuleAbstract
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
       // $ruleId = $this->request->input('rule_id');
       // $this->instanceRule = DeviceCvedixrtInstanceRule::query()->findOrFail($ruleId);

        try {
            $this->instanceRule->update($this->data);

            return $this->instanceRule;
        } catch (QueryException $exception) {
            throw new Exception(
                __('cvedixrt-instance-update.update.error.database', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (ValidationException $exception) {
            throw new Exception(
                __('cvedixrt-instance-update.update.error.validation', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (TypeError $exception) {
            throw new Exception(
                __('cvedixrt-instance-update.update.error.type', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (Throwable $exception) {
            throw new Exception(
                __('cvedixrt-instance-update.update.error.unknown', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        }

    }
}
