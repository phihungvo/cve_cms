<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Action;

use App\Domains\UserGroup\Model\GroupModel as Model;
use Exception;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use TypeError;

/**
 * User Group Update Action
 *
 * Handles the business logic for updating user group data
 */
class UpdateAction extends CreatUpdateActionAbstract
{
    /**
     * Save the updated user group data to the database
     *
     * Updates the user group with the name and description from the provided data
     *
     * @return Model
     * @throws Exception When any unexpected errors occur
     */
    protected function save(): Model
    {
        try {
            $this->row->update([
                'name' => $this->data['name'],
                'description' => $this->data['description'],
                'enterprise_id' => $this->data['enterprise_id'] ?? null,
            ]);

            return $this->row;
        } catch (QueryException $exception) {
            throw new Exception(
                __('user-group-update.update.database-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (ValidationException $exception) {
            throw new Exception(
                __('user-group-update.update.validation-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (TypeError $exception) {
            throw new Exception(
                __('user-group-update.update.type-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (Throwable $exception) {
            // Fallback for any other exceptions
            throw new Exception(
                __('user-group-update.update.error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        }
    }
}
