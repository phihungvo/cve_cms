<?php declare(strict_types=1);

namespace App\Domains\CvedixrtGroup\Action;

use App\Domains\CvedixrtGroup\Model\CvedixrtGroupModel as Model;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use PDOException;
use RuntimeException;
use Throwable;

class CreateAction extends CreateUpdateAbstractAction
{
    /**
     * Create CvedixrtGroup
     *
     * @return Model
     *
     * @override
     */
    protected function save(): Model
    {
        try {
            $this->row = Model::query()->create($this->data);

            return $this->row;
        } catch (AuthorizationException $e) {
            throw new RuntimeException(
                __('cvedixrt-group-create.error.unauthorized', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (PDOException|QueryException $e) {
            throw new RuntimeException(
                __('cvedixrt-group-create.error.database', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ValidationException $e) {
            throw new RuntimeException(
                __('cvedixrt-group-create.error.validation-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new RuntimeException(
                __('cvedixrt-group-create.error.unknown-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}
