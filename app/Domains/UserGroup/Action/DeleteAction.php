<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Action;

use Exception;
use Illuminate\Database\QueryException;
use PDOException;
use Throwable;
use Illuminate\Support\Facades\Lang;

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
     * Xóa mềm Group
     *
     * @throws Exception
     */
    protected function delete(): void
    {
        try {
            // Kiểm tra xem Group có đang được sử dụng trong bảng UserGroup hay không
            if ($this->row->userGroups()->exists()) {
                throw new Exception(__('user-group-update.delete.error.in-use'));
            }
            $this->row->delete();
        } catch (QueryException $e) {
            // Typically occurs with foreign key constraint violations
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                throw new Exception(__('user-group-update.delete.error.in-use'), 0, $e);
            }
            throw new Exception(__('user-group-update.delete.error.query'), 0, $e);
        } catch (PDOException $e) {
            // Database connection issues
            throw new Exception(__('user-group-update.delete.error.connection'), 0, $e);
        } catch (Throwable $e) {
            // Generic exception handler with added context
            throw new Exception(__('user-group-update.delete.error.unexpected', ['message' => $e->getMessage()]), 0, $e);
        }
    }
}
