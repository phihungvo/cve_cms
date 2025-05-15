<?php declare(strict_types=1);

namespace App\Domains\CvedixInstance\Action;

use App\Domains\CvedixInstance\Model\CvedixInstanceModel as Model;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use PDOException;
use RuntimeException;
use Throwable;

class CreateAction extends CreateUpdateAbstractAction
{
    /**
     * Create CvedixInstance
     *
     * @return Model
     *
     * @override
     */
    protected function save(): Model
    {
        try {
            $this->row = Model::query()->create([
                // TODO: Replace with actual fields

            ]);

            return $this->row;
        } catch (AuthorizationException $e) {
            // Lỗi xảy ra khi người dùng không có quyền thực hiện hành động này
            throw new RuntimeException(
                __('cvedix-instance-create.error.unauthorized', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (PDOException|QueryException $e) {
            // Lỗi xảy ra khi có vấn đề với cơ sở dữ liệu (ví dụ: vi phạm ràng buộc)
            throw new RuntimeException(
                __('cvedix-instance-create.error.database', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ValidationException $e) {
            // Lỗi xảy ra khi dữ liệu không hợp lệ
            throw new RuntimeException(
                __('cvedix-instance-create.validation-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            // Lỗi chung cho tất cả các ngoại lệ khác không được xử lý cụ thể
            throw new RuntimeException(
                __('cvedix-instance-create.unknown-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}

