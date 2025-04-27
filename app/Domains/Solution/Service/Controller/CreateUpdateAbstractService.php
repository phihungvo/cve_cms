<?php

namespace App\Domains\Solution\Service\Controller;

abstract class CreateUpdateAbstractService extends ControllerAbstract
{
    protected function request(): void
    {
        $this->requestMergeWithRow();
    }

    protected function dataCreateUpdate(): array
    {
        return [
            'device' => $this->device,
        ];
    }
}
