<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Action;

use Exception;
use Illuminate\Database\QueryException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ForceDeleteAction extends ActionAbstract
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->forceDelete();
    }

    /**
     * Xóa cứng Group
     *
     * @throws Exception
     */
    protected function forceDelete(): void
    {
        try {
            // Kiểm tra xem Group có đang được sử dụng trong bảng UserGroup hay không
            if ($this->row->userGroups()->exists()) {
                throw new Exception(__('user-group-update.force-delete.error.in-use'));
            }
            $this->row->forceDelete();
        } catch (ModelNotFoundException $e) {
            throw new Exception(__('user-group-update.force-delete.error.model-not-found'));
        } catch (QueryException $e) {
            throw new Exception(__('user-group-update.force-delete.error.query-error'));
        } catch (Exception $e) {
            throw new Exception(__('user-group-update.force-delete.error.unexpected-error'));
        }
    }
}
