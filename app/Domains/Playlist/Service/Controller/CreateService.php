<?php

namespace App\Domains\Playlist\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class CreateService extends CreateUpdateAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->request();
    }

    /**
     * Data Create Playlist
     *
     * @return array
     *
     * @override
     */
    public function data(): array
    {
        return $this->dataCreateUpdate();
    }
}
