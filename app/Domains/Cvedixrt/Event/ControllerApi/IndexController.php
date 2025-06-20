<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\ControllerApi;

use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel as Model;
use Illuminate\Http\JsonResponse;

class IndexController extends ControllerApiAbstract
{
    public function __invoke(): JsonResponse
    {
        return $this->json($this->factory('Cvedixrt\Event')->fractal('json', $this->data()));
    }

    protected function data()
    {
        return Model::query()->list()->get();
    }
}
