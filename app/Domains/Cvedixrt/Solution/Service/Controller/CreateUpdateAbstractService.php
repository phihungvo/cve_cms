<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Solution\Service\Controller;

use App\Domains\Cvedixrt\Solution\Service\Controller\ControllerAbstract;

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
