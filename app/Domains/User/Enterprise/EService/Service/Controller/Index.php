<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use App\Domains\User\Enterprise\EService\Model\Collection\EService as Collection;
use App\Domains\User\Enterprise\EService\Model\EService as Model;

class Index extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
    }

    public function data(): array
    {
        return [
            ...$this->dataCore(),
            'services' => $this->list(),
        ];
    }
    /**
     * @return \App\Domains\User\Enterprise\EService\Model\Collection\EService
     */
    public function list(): Collection
    {
        return new Collection(Model::query()->get()->all());
    }

}