<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Domains\Device\Model\Device;

class EventBroadcast extends ControllerAbstract
{
    public function __construct(protected Request $reqeust, Authenticatable $auth, Device $row)
    {
        $this->row = $row;
        $this->filters();
    }

    protected function filters()
    {
    }

    public function data(): Collection
    {
        return $this->row->events()
            ->get()
            ->map(function ($event) {
                // Lấy thêm 'priority' ở 'instanceRule'
                $event->priority = $event->instanceRule->priority ?? null;
                unset($event->instanceRule);

                return $event;
            });
    }
}
