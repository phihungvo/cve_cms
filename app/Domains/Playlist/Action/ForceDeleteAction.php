<?php declare(strict_types=1);

namespace App\Domains\Playlist\Action;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Throwable;

class ForceDeleteAction extends ActionAbstract
{
    public function handle(): void
    {
        $this->forceDelete();
    }

    /**
     * Force Delete action
     *
     * @return void
     * @throws Exception
     */
    protected function forceDelete(): void
    {
        try {
            $this->row->forceDelete();
        } catch (ModelNotFoundException $e) {
            throw new Exception(
                __('playlist-update.force-delete.error.model-not-found'),
                0,
                $e
            );
        } catch (QueryException $e) {
            throw new Exception(
                __('playlist-update.force-delete.error.query-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new Exception(
                __('playlist-update.force-delete.error.unexpected-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}
