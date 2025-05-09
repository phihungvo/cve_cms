<?php declare(strict_types=1);

namespace App\Domains\PlaylistGroup\Action;

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
            $this->row->forceDelete();
        } catch (ModelNotFoundException $e) {
            throw new Exception(__('playlist-group-force-delete.error.model-not-found'));
        } catch (QueryException $e) {
            throw new Exception(__('playlist-group-force-delete.error.query-error'));
        } catch (Exception $e) {
            throw new Exception(__('playlist-group-force-delete.error.unexpected-error'));
        }
    }
}
