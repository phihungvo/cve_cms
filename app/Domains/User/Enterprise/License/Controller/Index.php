<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\License\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Domains\User\Enterprise\License\Service\Controller\Index as ControllerLicense;

class Index extends ControllerAbstract
{
    public function __invoke(): Response|JsonResponse
    {
        $service = new ControllerLicense($this->request, $this->auth);

        if ($this->request->wantsJson()) {
            return $this->responseJson($service);
        }

        $this->meta('title', __('eservice-index.meta-title'));

        return $this->page('user.enterprise.license.index', $service->data());
    }

    protected function responseJson(ControllerLicense $service): JsonResponse
    {
        return $this->json($this->factory()->fractal('simple', $service->list()));
    }
}
