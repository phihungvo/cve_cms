<?php declare(strict_types=1);

namespace App\Domains\User\Permission\Action;

class Delete extends ActionAbstract
{
    public function handle(): void
    {
        $this->deletePermission();
    }

    protected function deletePermission(): void
    {
        $this->row->delete(); // Xóa Feature
    }
}