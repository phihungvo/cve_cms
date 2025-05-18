<?php declare(strict_types=1);

namespace App\Domains\DeviceGroup\Action;

use App\Domains\DeviceGroup\Model\DeviceGroupModel as Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use PDOException;
use RuntimeException;
use Throwable;

class CreateAction extends CreateUpdateAbstractAction
{
    /**
     * Create DeviceGroup
     *
     * @return Model
     *
     * @override
     */
    protected function save(): Model
    {
        try {
            $this->row = Model::query()
                ->create($this->data);

            return $this->row;
        } catch (PDOException|QueryException $e) {
            throw new RuntimeException(
                __('device-group-create.error.database', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ModelNotFoundException $e) {
            throw new RuntimeException(
                __('device-group-create.error.not-found', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ValidationException $e) {
            throw new RuntimeException(
                __('device-group-create.validation-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new RuntimeException(
                __('device-group-create.unknown-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}
