<?php declare(strict_types=1);

namespace App\Domains\Playlist\Action;

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
        $this->delete();
    }

    /**
     * Soft Delete
     *
     * @return void
     * @throws Exception
     */
    protected function delete(): void
    {
        try {
            $this->row->delete();
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                throw new Exception(
                    __('playlist-update.error.query-error', ['message' => $e->getMessage()]),
                    0,
                    $e
                );
            }
            throw new Exception(
                __('playlist-update.error.database-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (PDOException $e) {
            throw new Exception(
                __('playlist-update.error.connection-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new Exception(
                __('playlist-update.error.unexpected-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}
