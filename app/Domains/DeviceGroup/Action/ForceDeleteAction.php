<?php declare(strict_types=1);

namespace App\Domains\DeviceGroup\Action;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Throwable;

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
     * Force Delete action
     *
     * @throws Exception
     *
     * @return void
     */
    protected function forceDelete(): void
    {
        try {
            if ($this->row->deviceGroupMaps()->exists()) {
                throw new Exception(__('device-group-update.force-delete.error.in-use'));
            }
            $this->row->forceDelete();
        } catch (ModelNotFoundException $e) {
            throw new Exception(
                __('device-group-update.force-delete.error.model-not-found'),
                0,
                $e
            );
        } catch (QueryException $e) {
            throw new Exception(
                __('device-group-update.force-delete.error.query-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new Exception(
                __('device-group-update.force-delete.error.unexpected-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}
