<?php declare(strict_types=1);

namespace App\Domains\Playlist\Action;

class DeleteAction extends ActionAbstract
{
    public function handle(): void
    {
        $this->delete();
    }

    /**
     * Soft Delete
     *
     * @return void
     */
    protected function delete(): void
    {
        $this->row->delete();
    }
}
