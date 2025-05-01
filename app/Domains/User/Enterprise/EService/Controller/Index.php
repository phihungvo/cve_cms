<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Domains\User\Enterprise\EService\Service\Controller\Index as ControllerService;

class Index extends ControllerAbstract
{
    public function __invoke(): Response|JsonResponse
    {
        $service = new ControllerService($this->request, $this->auth);

        if ($this->request->wantsJson()) {
            return $this->responseJson($service);
        }

        $this->meta('title', __('eservice-index.meta-title'));

        return $this->page('user.enterprise.eservice.index', $service->data());
    }

    protected function responseJson(ControllerService $service): JsonResponse
    {
        return $this->json($this->factory()->fractal('simple', $service->list()));
    }
}
