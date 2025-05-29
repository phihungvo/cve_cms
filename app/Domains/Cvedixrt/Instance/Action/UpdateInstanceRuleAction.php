<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Action;

use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceRuleModel as InstanceRule;
use Exception;
use Throwable;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use TypeError;

/**
 * CvedixrtInstanceRule Update Action
 *
 * Handles the business logic for updating cvedixrt_instance_rule data
 */
class UpdateInstanceRuleAction extends CreateUpdateInstanceRuleAbstractAction
{
    /**
     * Save the updated cvedixrt_instance_rule data to the database
     *
     * Updates the cvedixrt_instance_rule with the provided data
     *
     * @throws Exception When any unexpected errors occur
     *
     * @return InstanceRule
     */
    protected function save(): InstanceRule
    {
        try {
            $this->instanceRule->update($this->data);

            return $this->instanceRule;
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
