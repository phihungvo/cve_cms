<?php declare(strict_types=1);

namespace App\Domains\Report\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Domains\Report\Service\ControllerApi\HealthSystem as ControllerService;

class HealthSystem
{
    /**
     * @param \Illuminate\Http\Request $request
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        return new JsonResponse($this->data());
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return (new ControllerService($this->request))->data();
    }
}