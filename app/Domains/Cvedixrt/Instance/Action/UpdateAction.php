<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Action;

use Exception;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use TypeError;

/**
 * CvedixrtInstance Update Action
 *
 * Handles the business logic for updating cvedixrt_instance data
 */
class UpdateAction extends CreateUpdateAbstractAction
{
    /**
     * Save the updated cvedixrt_instance data to the database
     *
     * Updates the cvedixrt_instance with the provided data
     *
     * @throws Exception When any unexpected errors occur
     *
     * @return void
     */
    protected function save(): void
    {
        try {
            $this->row->update($this->data);

        } catch (QueryException $exception) {
            // Lỗi xảy ra khi có vấn đề với truy vấn cơ sở dữ liệu (ví dụ: vi phạm ràng buộc khóa ngoại)
            throw new Exception(
                __('cvedixrt-instance-update.update.error.database', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (ValidationException $exception) {
            // Lỗi xảy ra khi dữ liệu không hợp lệ (vi phạm các quy tắc xác thực)
            throw new Exception(
                __('cvedixrt-instance-update.update.error.validation', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (TypeError $exception) {
            // Lỗi xảy ra khi kiểu dữ liệu không đúng (ví dụ: truyền sai kiểu dữ liệu vào hàm)
            throw new Exception(
                __('cvedixrt-instance-update.update.error.type', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (Throwable $exception) {
            // Lỗi chung cho tất cả các ngoại lệ khác không được xử lý cụ thể
            throw new Exception(
                __('cvedixrt-instance-update.update.error.unknown', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        }
    }
}
