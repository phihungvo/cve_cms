<?php declare(strict_types=1);

namespace App\Domains\CvedixGroup\Action;

use App\Domains\CvedixGroup\Model\CvedixGroupModel as Model;
use Exception;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use TypeError;

/**
 * CvedixGroup Update Action
 *
 * Handles the business logic for updating cvedix_group data
 */
class UpdateAction extends CreateUpdateAbstractAction
{
    /**
     * Save the updated cvedix_group data to the database
     *
     * Updates the cvedix_group with the provided data
     *
     * @return Model
     * @throws Exception When any unexpected errors occur
     */
    protected function save(): Model
    {
        try {
            $this->row->update([
                // TODO: Replace with actual fields

            ]);

            return $this->row;
        } catch (QueryException $exception) {
            // Lỗi xảy ra khi có vấn đề với truy vấn cơ sở dữ liệu (ví dụ: vi phạm ràng buộc khóa ngoại)
            throw new Exception(
                __('cvedix-group-update.update.database-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (ValidationException $exception) {
            // Lỗi xảy ra khi dữ liệu không hợp lệ (vi phạm các quy tắc xác thực)
            throw new Exception(
                __('cvedix-group-update.update.validation-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (TypeError $exception) {
            // Lỗi xảy ra khi kiểu dữ liệu không đúng (ví dụ: truyền sai kiểu dữ liệu vào hàm)
            throw new Exception(
                __('cvedix-group-update.update.type-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (Throwable $exception) {
            // Lỗi chung cho tất cả các ngoại lệ khác không được xử lý cụ thể
            throw new Exception(
                __('cvedix-group-update.update.error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        }
    }
}
