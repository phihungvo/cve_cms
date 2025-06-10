<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Group\Service\Controller;

abstract class CreateUpdateAbstractService extends ControllerAbstract
{
    /**
     * @return void
     */
    protected function request(): void
    {
        $this->requestMergeWithRow();
    }

    protected function dataCreateUpdate(): array
    {
        return [
            // TODO: Data shared between Create and Update operations
        ];
    }
}
