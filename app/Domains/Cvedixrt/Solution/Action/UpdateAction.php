<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Solution\Action;

use App\Domains\Cvedixrt\Solution\Action\CreateUpdateAbstractAction;
use App\Domains\Cvedixrt\Solution\Model\CvedixrtSolutionModel as Model;
use Exception;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use TypeError;

/**
 * CvedixrtSolution Update Action
 *
 * Handles the business logic for updating cvedixrt_solution data
 */
class UpdateAction extends CreateUpdateAbstractAction
{
    /**
     * Save the updated cvedixrt_solution data to the database
     *
     * Updates the cvedixrt_solution with the provided data
     *
     * @return Model
     * @throws Exception When any unexpected errors occur
     */
    protected function save(): Model
    {
        try {
            $this->row->update($this->data);

            return $this->row;
        } catch (QueryException $exception) {
            throw new Exception(
                __('cvedixrt-solution-update.update.error.query-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (ValidationException $exception) {
            throw new Exception(
                __('cvedixrt-solution-update.update.error.validation-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (TypeError $exception) {
            throw new Exception(
                __('cvedixrt-solution-update.update.error.type-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (Throwable $exception) {
            throw new Exception(
                __('cvedixrt-solution-update.update.error.unexpected-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        }
    }
}

