<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Action;

class Delete extends ActionAbstract
{
    public function handle(): void
    {
        $this->deleteEService();
    }

    protected function deleteEService(): void
    {
        $this->row->delete(); // Xóa Feature
    }
}