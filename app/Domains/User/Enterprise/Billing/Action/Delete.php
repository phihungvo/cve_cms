<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Action;

use App\Domains\User\Enterprise\Billing\Model\Billing;

class Delete extends ActionAbstract
{
    public function setRow(Billing $row): self
    {
        $this->row = $row;
        return $this;
    }

    public function handle(): void
    {
        $this->deleteLicense();
    }

    protected function deleteLicense(): void
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