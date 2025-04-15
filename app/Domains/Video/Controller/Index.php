<?php

declare(strict_types=1);

namespace App\Domains\Video\Controller;

use Illuminate\Http\Response;
use Illuminate\Http\JsonResponse;
use App\Domains\Video\Service\Controller\Index as ControllerService;

class Index extends ControllerAbstract
{
    /**
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function __invoke(): Response|JsonResponse
    {
        if ($this->request->wantsJson()) {
            return $this->json(['data' => ControllerService::new($this->request, $this->auth)->list()]);
        }

        $this->meta('title', __('video-index.meta-title'));
        return $this->page('video.index', $this->data()); // Sửa đường dẫn view
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
