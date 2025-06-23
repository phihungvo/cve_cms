<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Service\Controller;

use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EventBroadcast extends ControllerAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->filters();
    }

    protected function filters(): void
    {
        // Add filter logic here
    }

    public function data(): Collection
    {
        return CvedixrtEventModel::query()
            ->get()
            ->map(function ($event) {
                // Lấy thêm 'priority' ở 'instanceRule'
                $event->priority = $event->instanceRule->priority ?? null;
                unset($event->instanceRule);

                return $event;
            });
    }
}
