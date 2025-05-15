<?php declare(strict_types=1);

namespace App\Domains\CvedixInstance\Service\Controller;

use App\Domains\CvedixInstance\Model\CvedixInstanceModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected CvedixInstanceModel $row)
    {
        $this->request();
    }

    /**
     * Data update CvedixInstance
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
