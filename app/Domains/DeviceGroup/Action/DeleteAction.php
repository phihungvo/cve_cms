<?php declare(strict_types=1);

namespace App\Domains\DeviceGroup\Action;

use Exception;
use Illuminate\Database\QueryException;

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
     * Soft Delete
     *
     * @throws Exception
     *
     * @return void
     */
    protected function delete(): void
    {
        try {
            $this->row->delete();
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                throw new Exception(
                    __('device-group-update.delete.error.query-error', ['message' => $e->getMessage()]),
                    0,
                    $e
                );
            }
            throw new Exception(
                __('device-group-update.delete.error.database-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (PDOException $e) {
            throw new Exception(
                __('device-group-update.delete.error.connection-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new Exception(
                __('device-group-update.delete.error.unexpected-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }

    /**
     * @throws Exception
     */
    protected function check(): void
    {
        if ($this->row->deviceGroupMaps()->exists()) {
            throw new Exception(__('device-group-update.delete.error.in-use'));
        }
    }
}
