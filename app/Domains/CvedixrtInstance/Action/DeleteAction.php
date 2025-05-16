<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Action;

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
            throw new Exception(__(
                'cvedixrt-instance-update.delete.error.unauthorized',
                ['message' => $e->getMessage()]
            ), 0, $e);
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                throw new Exception(__('cvedixrt-instance-update.delete.error.in-use'), 0, $e);
            }
            throw new Exception(__(
                'cvedixrt-instance-update.delete.error.query-error',
                ['message' => $e->getMessage()]
            ), 0, $e);
        } catch (PDOException $e) {
            throw new Exception(__(
                'cvedixrt-instance-update.delete.error.connection',
                ['message' => $e->getMessage()]
            ), 0, $e);
        } catch (Throwable $e) {
            throw new Exception(__(
                'cvedixrt-instance-update.delete.error.unexpected-error',
                ['message' => $e->getMessage()]
            ), 0, $e);
        }
    }
}
