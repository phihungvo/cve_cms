<?php

declare(strict_types=1);

namespace App\Domains\User\Role\Controller;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use App\Domains\User\Role\Service\Controller\Index as ControllerService;

class Index extends ControllerAbstract
{
    /**
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     */
    public function __invoke(): Response|JsonResponse
    {
        if ($this->request->wantsJson()) {
            return $this->responseJson();
        }

        $this->meta('title', __('role-index.meta-title'));

        $data = $this->getService()->data();
        // dd($data); // Kiểm tra dữ liệu nếu cần
        return $this->page('user.role.index', $data);
    }

    /**
     * Get JSON response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function responseJson(): JsonResponse
    {
        return $this->json([
            'data' => $this->getService()->responseJsonList()->map(fn($role) => $this->getService()->formatRole($role))->all()
        ]);
    }

    /**
     * Get the service instance.
     *
     * @return \App\Domains\User\Role\Service\Controller\Index
     */
    protected function getService()
    {
        return ControllerService::new($this->request, $this->auth);
    }
}
