<?php declare(strict_types=1);

namespace App\Domains\User\Action;

use App\Exceptions\ValidatorException;

class Delete extends ActionAbstract
{
    /**
     * @throws ValidatorException
     *
     * @return void
     */
    public function handle(): void
    {
        $this->check();
        $this->delete();
    }

    /**
     * @throws ValidatorException
     *
     * @return void
     */
    protected function check(): void
    {
        if ($this->row->id === $this->auth->id) {
            $this->exceptionValidator(__('user-delete.error.current'));
        }
    }

    /**
     * @return void
     */
    protected function delete(): void
    {
        $this->row->delete();
    }
}
