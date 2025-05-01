<?php declare(strict_types=1);

namespace App\Domains\Group\Action;

use Exception;
use Illuminate\Database\QueryException;

class Delete extends ActionAbstract
{
    /**
     * @throws Exception
     */
    public function handle(): void{
        $this->delete();
    }

    /**
     * Hard delete the group.
     *
     * @throws Exception
     */
    protected function delete(): void
    {
        try {
            $this->row->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000') {
                throw new Exception(__('group-update.error.foreign_key_constraint'));
            }
            throw $e;
        }
    }
}
