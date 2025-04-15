<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Contracts\View\View;
use App\Domains\Campaign\Service\Controller\Index as ControllerService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract;

class Index extends ControllerWebAbstract
{
    /**
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse|\Illuminate\Contracts\View\View
     */
    public function __invoke(): Response|JsonResponse|View
    {
        if ($this->request->wantsJson()) {
            return $this->responseJson();
        }

        $this->meta('title', __('campaign-index.meta-title'));

        $data = $this->getService()->data();

        return $this->page('campaign.index', $data);
    }

    /**
     * Get JSON response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseJson(): JsonResponse
    {
        return $this->json([
            'data' => $this->getService()->responseJsonList()->map(fn ($campaign) => $this->getService()->formatCampaign($campaign))->all(),
        ]);
    }

    /**
     * Get the service instance.
     *
     * @return \App\Domains\Campaign\Service\Controller\Index
     */
    protected function getService(): ControllerService
    {
        return ControllerService::new($this->request, $this->auth);
    }
}
