<?php declare(strict_types=1);

namespace App\Domains\CvedixSolution\Service\Controller;

use App\Domains\CvedixSolution\Model\CvedixSolutionModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected CvedixSolutionModel $row)
    {
        $this->request();
    }

    /**
     * Data update CvedixSolution
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
