<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Service\Controller;

use App\Domains\Campaign\Media\Model\Media as MediaModel;
use Illuminate\Database\Eloquent\Collection;

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
