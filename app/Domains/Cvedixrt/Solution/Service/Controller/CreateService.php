<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Solution\Service\Controller;

use App\Domains\Cvedixrt\Solution\Service\Controller\CreateUpdateAbstractService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class CreateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->request();
    }

    /**
     * Data for creating CvedixrtSolution
     *
     * @return array
     */
    public function data(): array
    {
        return $this->dataCreateUpdate() + [
        ];
    }
}
