<?php declare(strict_types=1);

namespace App\Domains\CvedixGroup\Service\Controller;

use App\Domains\CvedixGroup\Model\CvedixGroupModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected CvedixGroupModel $row)
    {
        $this->request();
    }

    /**
     * Data update CvedixGroup
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
