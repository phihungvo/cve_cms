<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controller;

use App\Domains\Notification\Service\Controller\Create as CreateService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;

class Create extends ControllerAbstract
{
    public function __invoke(): RedirectResponse
    {
        try {
            $service = CreateService::new($this->request, $this->auth);
            $service->create();

            $this->sessionMessage('success', __('notification-create.success'));

            return redirect()->route('notification.index');
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->route('notification.index');
        }
    }
}