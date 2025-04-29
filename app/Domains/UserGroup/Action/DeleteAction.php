<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Action;

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
     * Xóa mềm Group
     *
     * @throws Exception
     */
    protected function delete(): void
    {
        try {
            $this->row->delete();
        } catch (QueryException $e) {
            // Typically occurs with foreign key constraint violations
            if (str_contains($e->getMessage(), 'foreign key constraint fails')) {
                throw new Exception('Cannot delete this user group because it is still in use.', 0, $e);
            }
            throw new Exception('Database query error occurred during deletion.', 0, $e);
        } catch (PDOException $e) {
            // Database connection issues
            throw new Exception('Database connection error occurred during deletion.', 0, $e);
        } catch (Throwable $e) {
            // Generic exception handler with added context
            throw new Exception('Failed to delete user group: '.$e->getMessage(), 0, $e);
        }
    }
}
