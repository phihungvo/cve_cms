<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Service\Controller;

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
        ];
    }
}
