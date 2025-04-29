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
 *
 * @package App\Domains\UserGroup\Action
 */
class UpdateAction extends CreatUpdateActionAbstract
{
    /**
     * Save the updated user group data to the database
     *
     * Updates the user group with the name and description from the provided data
     *
     * @return void
     * @throws Exception When database errors occur during update
     * @throws Exception When validation fails
     * @throws Exception When required data is missing
     * @throws Exception When type errors occur
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
            throw new Exception('Database error during user group update: '.$exception->getMessage(), 0, $exception);
        } catch (ValidationException $exception) {
            throw new Exception('Validation failed for user group update: '.$exception->getMessage(), 0, $exception);
        } catch (TypeError $exception) {
            throw new Exception('Type error in user group data: '.$exception->getMessage(), 0, $exception);
        } catch (Throwable $exception) {
            // Fallback for any other exceptions
            throw new Exception('Unexpected error during user group update: '.$exception->getMessage(), 0, $exception);
        }
    }
}
