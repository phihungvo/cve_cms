<?php declare(strict_types=1);

namespace App\Domains\CvedixrtSolution\Action;

use App\Domains\CvedixrtSolution\Model\CvedixrtSolutionModel as Model;
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
                __('cvedixrt-solution-update.update.database-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (ValidationException $exception) {
            throw new Exception(
                __('cvedixrt-solution-update.update.validation-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (TypeError $exception) {
            throw new Exception(
                __('cvedixrt-solution-update.update.type-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (Throwable $exception) {
            throw new Exception(
                __('cvedixrt-solution-update.update.error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        }
    }
}
