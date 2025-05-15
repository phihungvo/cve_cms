<?php declare(strict_types=1);

namespace App\Domains\CvedixrtSolution\Action;

use Exception;
use Illuminate\Database\QueryException;
use PDOException;
use Throwable;
use Illuminate\Auth\Access\AuthorizationException;

class DeleteAction extends ActionAbstract
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->delete();
    }

    /**
     * Soft Delete
     *
     * @throws Exception
     */
    protected function delete(): void
    {
        try {
            $this->row->delete();
        } catch (AuthorizationException $e) {
            // Lỗi phân quyền: xảy ra khi người dùng không có quyền xóa bản ghi này
            throw new Exception(__('cvedixrt-solution-delete.error.unauthorized', ['message' => $e->getMessage()]), 0, $e);
        } catch (QueryException $e) {
            // Lỗi truy vấn SQL: thường xảy ra khi vi phạm ràng buộc khóa ngoại
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                // Lỗi khóa ngoại: xảy ra khi bản ghi đang được sử dụng ở bảng khác
                throw new Exception(__('cvedixrt-solution-delete.error.in-use'), 0, $e);
            }
            throw new Exception(__('cvedixrt-solution-delete.error.query'), 0, $e);
        } catch (PDOException $e) {
            // Lỗi kết nối cơ sở dữ liệu: xảy ra khi không thể kết nối đến database
            throw new Exception(__('cvedixrt-solution-delete.error.connection'), 0, $e);
        } catch (Throwable $e) {
            // Lỗi không xác định: bắt tất cả các ngoại lệ khác không được xử lý cụ thể ở trên
            throw new Exception(__('cvedixrt-solution-delete.error.unexpected', ['message' => $e->getMessage()]), 0, $e);
        }
    }
}
