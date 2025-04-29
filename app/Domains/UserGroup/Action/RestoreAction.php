<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Action;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use PDOException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Validation\ValidationException;

class RestoreAction extends ActionAbstract {
    /**
     * @throws Exception
     */
    public function handle():void
    {
        $this->restore();
    }

    /**
     * Khôi phục Group
     *
     * @throws Exception
     */
    protected function restore():void
    {
        try {
            $this->row->restore();
        } catch (ModelNotFoundException $e) {
            throw new Exception('User group not found or already deleted permanently: ' . $e->getMessage(), 0, $e);
        } catch (QueryException $e) {
            throw new Exception('Database error while restoring user group: ' . $e->getMessage(), 0, $e);
        } catch (PDOException $e) {
            throw new Exception('Database connection error during restore: ' . $e->getMessage(), 0, $e);
        } catch (AuthorizationException $e) {
            throw new Exception('Not authorized to restore this user group: ' . $e->getMessage(), 0, $e);
        } catch (ValidationException $e) {
            throw new Exception('Validation failed during user group restore: ' . $e->getMessage(), 0, $e);
        } catch (Exception $e) {
            throw new Exception('Unexpected error during user group restore: ' . $e->getMessage(), 0, $e);
        }
    }
}
