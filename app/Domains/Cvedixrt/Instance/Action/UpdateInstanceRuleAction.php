<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Action;

use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceRuleModel as InstanceRule;
use Exception;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use TypeError;

/**
 * CvedixrtInstanceRule Update Action
 *
 * Handles the business logic for updating cvedixrt_instance_rule data
 */
class UpdateInstanceRuleAction extends CreateUpdateInstanceRuleAbstractAction
{
    /**
     * Save the updated cvedixrt_instance_rule data to the database
     *
     * Updates the cvedixrt_instance_rule with the provided data
     *
     * @throws Exception When any unexpected errors occur
     *
     * @return void
     */
    protected function save(): void
    {
        $ruleId = $this->request->input('rule_id');
        $this->instanceRule = InstanceRule::where('id', $ruleId)->firstOrFail();

        try {
            $this->instanceRule->update($this->data);
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
