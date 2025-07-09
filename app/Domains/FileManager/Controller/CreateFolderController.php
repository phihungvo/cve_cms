<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Controller;

use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use App\Domains\FileManager\Service\Controller\CreateFolderService as Service;
use Illuminate\Http\JsonResponse;

class CreateFolderController extends ControllerAbstract
{
    /**
     * Handle the folder creation request.
     *
     * @return JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        $service = Service::new($this->request, $this->auth);
        $response = $service->create();

        // Set session message if creation is successful
        if ($response->getStatusCode() === 201) {
            $this->sessionMessage('success', 'Tạo thư mục thành công: '.$this->request->input('name'));
        }

        return $response;
    }
}
