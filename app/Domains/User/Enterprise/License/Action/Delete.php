<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\License\Action;

use App\Domains\User\Enterprise\License\Model\License;

class Delete extends ActionAbstract
{
    public function setRow(License $row): self
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