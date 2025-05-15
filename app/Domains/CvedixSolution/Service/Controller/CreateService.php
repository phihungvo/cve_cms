<?php declare(strict_types=1);

namespace App\Domains\CvedixSolution\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class CreateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->request();
    }

    /**
     * Data for creating CvedixSolution
     *
     * @return array
     */
    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            //  TODO: Add data for creating CvedixSolution
        ];
    }
}
