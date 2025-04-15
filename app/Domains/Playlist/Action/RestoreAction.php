<?php

namespace App\Domains\Playlist\Action;

class RestoreAction extends ActionAbstract
{
    public function handle(): void
    {
        $this->restore();
    }

    /**
     * Restore action
     *
     * @return void
     */
    protected function restore(): void
    {
        $this->row->restore();
    }
}
