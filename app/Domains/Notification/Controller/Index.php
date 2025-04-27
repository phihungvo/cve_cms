<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controller;

use App\Domains\Notification\Action\Delete;
use App\Domains\Notification\Service\Controller\Index as ControllerService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;

class Index extends ControllerAbstract
{
    public function __invoke(): Response
    {
        $this->meta('title', __('notification-index.meta-title'));

        return $this->page('notification.index', $this->data());
    }

    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    public function destroy(): RedirectResponse
    {
        $notificationId = $this->request->input('notification_id');
        $action = new Delete();
        $result = $action->handle($notificationId, $this->auth);

        return $this->redirectResult($result, 'notification.index');
    }

    public function restore($id): RedirectResponse
    {
        $action = new Delete();
        $result = $action->restore($id, $this->auth);

        return $this->redirectResult($result, 'notification.index');
    }

    public function forceDelete($id): RedirectResponse
    {
        $action = new Delete();
        $result = $action->forceDelete($id, $this->auth);

        return $this->redirectResult($result, 'notification.index');
    }

    protected function redirectResult(array $result, string $route): RedirectResponse
    {
        if ($result['success']) {
            $this->sessionMessage('success', $result['message']);
        } else {
            $this->sessionMessage('error', $result['message']);
        }
        return redirect()->route($route);
    }
}