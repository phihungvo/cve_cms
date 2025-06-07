<?php declare(strict_types=1);

namespace App\Domains\Playlist\PlaylistGroup\Action;

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
        $this->check();
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
            throw new Exception(__('playlist-group-update.force-delete.error.model-not-found'));
        } catch (QueryException $e) {
            throw new Exception(__('playlist-group-update.force-delete.error.query-error'));
        } catch (Exception $e) {
            throw new Exception(__('playlist-group-update.force-delete.error.unexpected-error'));
        }
    }

    /**
     * @throws Exception
     */
    protected function check(): void
    {
        if ($this->row->playlistGroupMaps()->exists()) {
            throw new Exception(__('playlist-group-update.force-delete.error.in-use'));
        }
    }
}
