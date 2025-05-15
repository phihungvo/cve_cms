<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controller;

use App\Domains\Notification\Model\Notification;
use App\Domains\Notification\Service\Controller\Update as UpdateService;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class Update extends ControllerAbstract
{
    public function __invoke(Request $request, int $id): Response|RedirectResponse
    {
        $notification = Notification::findOrFail($id);

        $this->meta('title', __('notification-update.meta-title'));

        // Xử lý cả GET và PATCH
        if ($request->isMethod('patch')) {
            return $this->update($request, $notification);
        }

        $service = UpdateService::new($request, $this->auth, $notification);

        return $this->page('notification.update', $service->data());
    }

    protected function update(Request $request, Notification $notification): RedirectResponse
    {
        $service = UpdateService::new($request, $this->auth, $notification);

        try {
            $service->update();
            $this->sessionMessage('success', __('notification-update.success'));

            return redirect()->route('notification.update', $notification->id);
        } catch (ValidationException $e) {
            Log::error('Validation failed: ', $e->errors());
            return redirect()->back()->withInput()->withErrors($e->errors());
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}