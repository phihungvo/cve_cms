<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Group\Action;

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
     * Hard Delete
     *
     * @throws Exception
     */
    protected function delete(): void
    {
        try {
            $this->row->delete();
        } catch (AuthorizationException $e) {
            throw new Exception(__('cvedixrt-group-update.delete.error.unauthorized', ['message' => $e->getMessage()]), 0, $e);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                throw new Exception(__('cvedixrt-group-update.delete.error.in-use'), 0, $e);
            }
            throw new Exception(__('cvedixrt-group-update.delete.error.query'), 0, $e);
        } catch (PDOException $e) {
            throw new Exception(__('cvedixrt-group-update.delete.error.connection'), 0, $e);
        } catch (Throwable $e) {
            throw new Exception(__('cvedixrt-group-update.delete.error.unexpected', ['message' => $e->getMessage()]), 0, $e);
        }
    }
}
