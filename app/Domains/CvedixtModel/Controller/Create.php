<?php

declare(strict_types=1);

namespace App\Domains\CvedixtModel\Controller;

use App\Domains\CvedixtModel\Service\Controller\Create as CreateService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;

class Create extends ControllerAbstract
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
            $message = __('cvedixrt-model.upload_success', ['count' => count($createdModel)]);
            $this->sessionMessage('success', $message);

            return redirect()->route('cvedixrt_model.index');
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());

            return redirect()->route('cvedixrt_model.index');
        }
    }
}
