<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Service\Controller;

use App\Domains\CvedixrtInstance\Model\CvedixrtInstanceModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected CvedixrtInstanceModel $row)
    {
        $this->request();
    }

    /**
     * Data update CvedixrtInstance
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
