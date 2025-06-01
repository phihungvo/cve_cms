<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Action;

use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceRuleModel as InstanceRule;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use PDOException;
use RuntimeException;
use Throwable;

class CreateInstanceRuleAction extends CreateUpdateInstanceRuleAbstractAction
{
    /**
     * Create CvedixrtInstanceRule
     *
     * @return InstanceRule
     *
     * @override
     */
    protected function save(): InstanceRule
    {
        try {
            $this->instanceRule = InstanceRule::query()->create($this->data);

            return $this->instanceRule;
        } catch (AuthorizationException $e) {
            throw new RuntimeException(
                __('cvedixrt-instance-create.error.unauthorized', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (PDOException|QueryException $e) {
            throw new RuntimeException(
                __('cvedixrt-instance-create.error.database', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ValidationException $e) {
            throw new RuntimeException(
                __('cvedixrt-instance-create.error.validation-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new RuntimeException(
                __('cvedixrt-instance-create.unknown-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}

