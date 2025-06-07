<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Group\Action;

use App\Domains\Cvedixrt\Group\Model\CvedixrtGroupModel as Model;
use Exception;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use TypeError;

/**
 * CvedixrtGroup Update Action
 *
 * Handles the business logic for updating cvedixrt_group data
 */
class UpdateAction extends CreateUpdateAbstractAction
{
    /**
     * Save the updated cvedixrt_group data to the database
     *
     * Updates the cvedixrt_group with the provided data
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
                __('cvedixrt-group-update.update.database-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (ValidationException $exception) {
            throw new Exception(
                __('cvedixrt-group-update.update.validation-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (TypeError $exception) {
            throw new Exception(
                __('cvedixrt-group-update.update.type-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (Throwable $exception) {
            throw new Exception(
                __('cvedixrt-group-update.update.error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        }
    }
}
