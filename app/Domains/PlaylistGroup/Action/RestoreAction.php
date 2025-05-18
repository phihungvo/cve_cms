<?php declare(strict_types=1);

namespace App\Domains\PlaylistGroup\Action;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use PDOException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;
use Throwable;

class RestoreAction extends ActionAbstract
{
    public function handle(): void
    {
        $this->restore();
    }

    /**
     * Restore action
     *
     * @throws Exception
     */
    protected function restore(): void
    {
        try {
            $this->row->restore();
        } catch (ModelNotFoundException $e) {
            throw new Exception(
                __('playlist-group-update.restore.error.model-not-found'),
                0,
                $e
            );
        } catch (QueryException $e) {
            throw new Exception(
                __('playlist-group-update.restore.error.query-error'),
                0,
                $e
            );
        } catch (PDOException $e) {
            throw new Exception(
                __('playlist-group-update.restore.error.connection-error'),
                0,
                $e
            );
        } catch (AuthorizationException $e) {
            throw new Exception(
                __('playlist-group-update.restore.error.not-authorized'),
                0,
                $e
            );
        } catch (ValidationException $e) {
            throw new Exception(
                __('playlist-group-update.restore.error.validation-failed'),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new Exception(
                __('playlist-group-update.restore.error.unexpected-error'),
                0,
                $e
            );
        }
    }
}
