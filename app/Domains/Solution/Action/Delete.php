<?php

namespace App\Domains\Solution\Action;

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
     * @throws Exception
     */
    protected function delete(): void
    {
        try {
            $this->row->delete();
        } catch (QueryException $e) {
            if ($e->getCode() === '23000')
            {
                throw new Exception(__('solution-update.error.foreign_key_constraint'));
            }
            throw $e;
        }

    }
}
