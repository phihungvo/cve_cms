<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Action;

use App\Domains\ScheduleGroup\Model\ScheduleGroupModel as Model;
use Exception;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use TypeError;

/**
 * ScheduleGroup Update Action
 *
 * Handles the business logic for updating schedule_group data
 */
class UpdateAction extends CreateUpdateAbstractAction
{
    /**
     * Save the updated schedule_group data to the database
     *
     * Updates the schedule_group with the provided data
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
                __('schedule-group-update.update.database-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (ValidationException $exception) {
            // Lỗi xảy ra khi dữ liệu không hợp lệ (vi phạm các quy tắc xác thực)
            throw new Exception(
                __('schedule-group-update.update.validation-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (TypeError $exception) {
            // Lỗi xảy ra khi kiểu dữ liệu không đúng (ví dụ: truyền sai kiểu dữ liệu vào hàm)
            throw new Exception(
                __('schedule-group-update.update.type-error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        } catch (Throwable $exception) {
            // Lỗi chung cho tất cả các ngoại lệ khác không được xử lý cụ thể
            throw new Exception(
                __('schedule-group-update.update.error', ['message' => $exception->getMessage()]),
                0,
                $exception
            );
        }
    }
}
