<?php declare(strict_types=1);

namespace App\Domains\VehicleGroup\Action;

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
     * Force Delete action
     *
     * @throws Exception
     */
    protected function forceDelete(): void
    {
        try {
            if ($this->row->vehicleGroupsMaps()->exists()) {
                throw new Exception(__('vehicle-group-update.delete.error.in-use'));
            }
            $this->row->forceDelete();
        } catch (ModelNotFoundException $e) {
            throw new Exception(__('vehicle-group-force-delete.error.model-not-found'));
        } catch (QueryException $e) {
            throw new Exception(__('vehicle-group-force-delete.error.query-error'));
        } catch (Exception $e) {
            throw new Exception(__('vehicle-group-force-delete.error.unexpected-error'));
        }
    }
}
