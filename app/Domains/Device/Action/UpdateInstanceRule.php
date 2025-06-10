<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Domains\Device\Model\DeviceCvedixrtInstanceRule;
use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Throwable;
use TypeError;

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
        try {
            $this->instanceRule->update($this->data);

            return $this->instanceRule;
        } catch (QueryException $exception) {
            // Check for specific database errors
            // If the error is related to a unique constraint on the 'uuid' field,
            if (str_contains($exception->getMessage(), 'for key \'uuid\'')) {
                throw new Exception(__('rt-analytics-rules.error.uuid_exists'));
            }

            throw new Exception(
                __('rt-analytics-rules.error.database'),
                0,
                $exception
            );
        } catch (ValidationException $exception) {
            throw new Exception(
                __('rt-analytics-rules.error.validation'),
                0,
                $exception
            );
        } catch (TypeError $exception) {
            throw new Exception(
                __('rt-analytics-rules.error.type'),
                0,
                $exception
            );
        } catch (Throwable $exception) {
            throw new Exception(
                __('rt-analytics-rules.error.unknown'),
                0,
                $exception
            );
        }

    }
}
