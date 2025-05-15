<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class CreateService extends CreateUpdateAbstract {

    public function __construct(protected Request $request, Authenticatable $auth)
    {
        $this->request();
    }

    public function data(): array
    {
        return $this->dataCreateUpdate();
    }
}
