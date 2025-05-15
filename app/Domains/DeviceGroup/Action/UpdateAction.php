<?php declare(strict_types=1);

namespace App\Domains\DeviceGroup\Action;

use App\Domains\DeviceGroup\Model\DeviceGroupModel as Model;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PDOException;
use RuntimeException;
use Throwable;

class UpdateAction extends CreateUpdateAbstractAction
{
    /**
     * Update DeviceGroup
     *
     * @return Model
     *
     * @override
     */
    protected function save(): Model
    {
        try {
            $this->row->update($this->data);
            return $this->row;
        } catch (PDOException|QueryException $e) {
            throw new RuntimeException(
                __('device-group-update.error.database', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ModelNotFoundException $e) {
            throw new RuntimeException(
                __('device-group-update.error.not-found', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ValidationException $e) {
            throw new RuntimeException(
                __('device-group-update.validation-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new RuntimeException(
                __('device-group-update.unknown-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}

