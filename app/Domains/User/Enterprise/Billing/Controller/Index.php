<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Domains\User\Enterprise\Billing\Service\Controller\Index as ControllerBilling;

class Index extends ControllerAbstract
{
    public function __invoke(): Response|JsonResponse
    {
        $billings = new ControllerBilling($this->request, $this->auth);

        if ($this->request->wantsJson()) {
            return $this->responseJson($billings);
        }

        $this->meta('title', __('billing.index.meta-title'));

        return $this->page('user.enterprise.billing.index', $billings->data());
    }

    protected function responseJson(ControllerBilling $billings): JsonResponse
    {
        return $this->json($this->factory()->fractal('simple', $billings->list()));
    }
}
