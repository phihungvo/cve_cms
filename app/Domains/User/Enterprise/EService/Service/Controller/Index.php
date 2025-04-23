<?php declare(strict_types=1);

namespace App\Domains\User\Permission\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\User\Permission\Model\Collection\Permission as Collection;
use App\Domains\User\Permission\Model\Permission as Model;

class Index extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
    }

    public function data(): array
    {
        return [
            ...$this->dataCore(),
            'permissions' => $this->list(),
        ];
    }
    /**
     * @return \App\Domains\User\Permission\Model\Collection\Permission
     */
    public function list(): Collection
    {
        return new Collection(Model::query()->get()->all());
    }

}