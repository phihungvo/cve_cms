<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Media\Controller;

use App\Domains\Campaign\Media\Service\Controller\Create as CreateService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;

class Create extends ControllerAbstract
{
    public function __invoke(): RedirectResponse
    {
        try {
            $service = CreateService::new($this->request, $this->auth);
            $createdMedia = $service->create();

            // Tạo thông báo thành công với số lượng file đã upload
            $message = __('media-create.upload-success', ['count' => count($createdMedia)]);
            $this->sessionMessage('success', $message);

            return redirect()->route('fpp.media.index');
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage()); // Thêm sessionMessage cho lỗi
            return redirect()->route('fpp.media.index');
        }
    }
}
