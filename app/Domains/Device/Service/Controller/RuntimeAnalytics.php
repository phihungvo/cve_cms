<?php declare(strict_types=1);

namespace App\Domains\Device\Service\Controller;

use App\Domains\Device\Model\Device as Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class RuntimeAnalytics extends ControllerAbstract
{

    public function __construct(protected Request $request, protected Authenticatable $auth, protected Model $row)
    {
    }

    public function data(): array
    {
        return [
          'row' => $this->row,
        ];
    }
}
