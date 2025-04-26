<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Action;

use App\Domains\User\Enterprise\EService\Model\EService;

class Delete extends ActionAbstract
{
    public function setRow(EService $row): self
    {
        $this->row = $row;
        return $this;
    }

    public function handle(): void
    {
        $this->deleteEService();
    }

    protected function deleteEService(): void
    {
        if ($this->row->trashed()) {
            // Bản ghi đã bị soft delete, tiến hành force delete
            $this->row->forceDelete();
        } else {
            // Bản ghi chưa bị soft delete, tiến hành soft delete
            $this->row->delete();
        }
    }
}