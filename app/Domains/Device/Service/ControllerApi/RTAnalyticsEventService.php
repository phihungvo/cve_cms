<?php declare(strict_types=1);

namespace App\Domains\Device\Service\ControllerApi;

use App\Domains\Device\Model\Device;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RTAnalyticsEventService extends ControllerApiAbstract
{
    public function __construct(protected Request $request, protected Authenticatable $auth, protected Device $row)
    {
        $this->filters();
    }

    protected function filters()
    {
    }

    public function data(): Collection
    {
        return $this->row->events()
            ->get();
    }
}
