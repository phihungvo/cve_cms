<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Service\Controller;

use App\Domains\UserGroup\Model\GroupModel as Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class UpdateService extends CreateUpdateAbstract
{
    public function __construct(protected Request $request, Authenticatable $auth, protected Model $row)
    {
        $this->request();
    }

    public function data(): mixed
    {
        return $this->dataCreateUpdate() + [
            'row' => $this->row,
        ];
    }
}
