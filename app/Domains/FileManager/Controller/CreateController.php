<?php

declare(strict_types=1);

namespace App\Domains\FileManager\Controller;

use App\Domains\FileManager\Service\Controller\CreateService as CreateService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;

class CreateController extends ControllerAbstract
{
    public function __invoke()
    {
        try {
            $service = CreateService::new($this->request, $this->auth);
            $createdModel = $service->create();

            // Đối với yêu cầu AJAX, trả về JsonResponse trực tiếp
            if ($this->request->ajax() || $this->request->wantsJson()) {
                return $createdModel;
            }

            // Đối với yêu cầu không AJAX, đếm số model đã tạo
            $message = __('file-manager.upload_success', ['count' => count($createdModel)]);
            $this->sessionMessage('success', $message);

            return redirect()->route('file_management.index');
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('file_management.index');
        }
    }
}
