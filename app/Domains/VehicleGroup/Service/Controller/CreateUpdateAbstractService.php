<?php declare(strict_types=1);

namespace App\Domains\VehicleGroup\Service\Controller;

use App\Domains\Campaign\Media\Model\Media as MediaModel;
use App\Domains\User\Enterprise\Model\Enterprise;
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
            'enterprises' => $this->enterprises(),
        ];
    }

    protected function enterprises(): Collection
    {
        return Enterprise::query()
            ->get();
    }
}
