<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Action;

use App\Domains\UserGroup\Model\GroupModel as Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use PDOException;
use RuntimeException;
use Throwable;

class CreateAction extends CreatUpdateActionAbstract
{
    /**
     * Lưu thông tin user group vào database
     *
     * @return Model
     */
    protected function save(): Model
    {
        try {
            $this->row = Model::query()->create([
                'name' => $this->data['name'],
                'description' => $this->data['description'],
                'enterprise_id' => $this->data['enterprise_id'] ?? null,
            ]);

            return $this->row;
        } catch (PDOException|QueryException $e) {
            throw new RuntimeException(
                __('user-group-create.error.database', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ModelNotFoundException $e) {
            throw new RuntimeException(
                __('user-group-create.error.not-found', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ValidationException $e) {
            throw new RuntimeException(
                __('user-group-create.validation-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new RuntimeException(
                __('user-group-create.unknown-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}
