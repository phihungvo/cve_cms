<?php declare(strict_types=1);

namespace App\Domains\User\Role\Feature\Action;


class Delete extends ActionAbstract
{
    public function handle(): void
    {
        $this->deleteFeature();
    }

    protected function deleteFeature(): void
    {
        $this->row->delete(); // Xóa Feature
    }
}