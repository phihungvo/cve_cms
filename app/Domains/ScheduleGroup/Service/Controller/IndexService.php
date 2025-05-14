<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Service\Controller;

use App\Domains\ScheduleGroup\Model\ScheduleGroupModel as Model;
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

    public function data(): array
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
