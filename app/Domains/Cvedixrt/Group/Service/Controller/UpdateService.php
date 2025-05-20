<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Group\Service\Controller;

use App\Domains\Cvedixrt\Group\Model\CvedixrtGroupModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstractService
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected CvedixrtGroupModel $row)
    {
        $this->request();
    }

    /**
     * Data update CvedixrtGroup
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
