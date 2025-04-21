<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use App\Domains\Device\Model\Device as Model;
use App\Domains\Device\Model\DeviceCveditInstance;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class RuntimeAnalytics extends ControllerAbstract
{

    public function __construct(protected Request $request, protected Authenticatable $auth, protected Model $row)
    {
    }

    public function data(): array
    {
        return [
            'row' => $this->row,
            'list' => $this->list(),
        ];
    }

    protected function list(): Collection
    {
        return DeviceCveditInstance::query()
            ->where('device_id', $this->row->id)
            ->get();
    }
}
