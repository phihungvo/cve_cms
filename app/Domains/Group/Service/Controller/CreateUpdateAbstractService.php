<?php declare(strict_types=1);

namespace App\Domains\Group\Service\Controller;

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
