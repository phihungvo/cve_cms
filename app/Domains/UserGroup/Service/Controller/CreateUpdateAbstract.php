<?php

namespace App\Domains\UserGroup\Service\Controller;

use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Support\Collection;

abstract class CreateUpdateAbstract extends ControllerAbstract
{
    protected function request(): void
    {
        $this->requestMergeWithRow();
    }

    protected function dataCreateUpdate(): array
    {
        return [
            'enterprises' => $this->enterprises(),
        ];
    }

    protected function enterprises(): Collection
    {
        return Enterprise::query()
            ->get();
    }
}
