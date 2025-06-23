<?php declare(strict_types=1);

namespace App\Domains\Device\ControllerApi;

use App\Domains\Device\Service\ControllerApi\RTAnalyticsEventService as ControllerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class RTAnalyticsEvent extends ControllerApiAbstract
{
    public function __invoke(int $id): JsonResponse
    {

        $this->row($id);

        return $this->json($this->factory()->fractal('mapEvent', $data));
    }

    protected function data(): Collection
    {
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }
}
