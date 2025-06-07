<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Solution\Service\Controller;

use App\Domains\Cvedixrt\Solution\Model\CvedixrtSolutionModel;
use App\Domains\Cvedixrt\Solution\Service\Controller\CreateUpdateAbstractService;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected CvedixrtSolutionModel $row)
    {
        $this->request();
    }

    /**
     * Data update CvedixrtSolution
     *
     * @return array
     */
    public function data(): array
    {
        return $this->dataCreateUpdate() + [
            'row' => $this->row,
            // and more ...
        ];
    }
}
