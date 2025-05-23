<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Action;

use Exception;
use Illuminate\Database\QueryException;
use PDOException;
use Throwable;

class DeleteAction extends ActionAbstract
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->check();
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
            $this->row->delete();
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                throw new Exception(__('user-group-update.delete.error.in-use'), 0, $e);
            }
            throw new Exception(__('user-group-update.delete.error.query'), 0, $e);
        } catch (PDOException $e) {
            throw new Exception(__('user-group-update.delete.error.connection'), 0, $e);
        } catch (Throwable $e) {
            throw new Exception(__('user-group-update.delete.error.unexpected', ['message' => $e->getMessage()]), 0, $e);
        }
    }

    /**
     * @throws Exception
     */
    protected function check(): void
    {
        if ($this->row->userGroups()->exists()) {
            throw new Exception(__('user-group-update.delete.error.in-use'));
        }
    }
}
