<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Media\Controller;

use App\Domains\Campaign\Media\Service\Controller\Create as CreateService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;

class Create extends ControllerAbstract
{
    public function __invoke()
    {
        try {
            $service = CreateService::new($this->request, $this->auth);
            $createdMedia = $service->create();

            // Đối với yêu cầu AJAX, trả về JsonResponse trực tiếp
            if ($this->request->ajax() || $this->request->wantsJson()) {
                return $createdMedia;
            }

            // Đối với yêu cầu không AJAX, đếm số media đã tạo
            $message = __('media-create.upload-success', ['count' => count($createdMedia)]);
            $this->sessionMessage('success', $message);

            return redirect()->route('fpp.media.index');
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->route('fpp.media.index');
        }
    }
}