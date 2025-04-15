<?php declare(strict_types=1);

namespace App\Domains\Playlist\Action;

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
     */
    protected function forceDelete(): void
    {
        $this->row->forceDelete();
    }
}
