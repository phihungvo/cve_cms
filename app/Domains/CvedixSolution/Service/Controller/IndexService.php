<?php declare(strict_types=1);

namespace App\Domains\CvedixSolution\Service\Controller;

use App\Domains\CvedixSolution\Model\CvedixSolutionModel as Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class IndexService extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->filters();
    }

    protected function filters(): void
    {
        // Add filter logic here
    }

    public function data(): mixed
    {
        return [
            'list' => $this->list(),
        ];
    }

    protected function list(): Collection
    {
        return Model::query()
            // TODO: Add filter conditions here
            ->get();
    }
}
