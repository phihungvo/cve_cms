<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controller;

use App\Domains\Notification\Service\Controller\Create as CreateService;
use App\Domains\Notification\Model\Notification;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class Create extends ControllerAbstract
{
    public function __invoke(Request $request): View|RedirectResponse
    {
        // Kiểm tra quyền tạo thông báo
        $query = Notification::query();
        $query->filterByPermission('access-notification-create');
        if (!$this->auth->hasRole('root') && !$this->auth->isOwner() && !$this->auth->hasPermission('access-notification-create')) {
            $this->sessionMessage('error', __('notification-create.no-permission'));
            return redirect()->route('notification.index');
        }

        // Nếu là yêu cầu GET, hiển thị form
        if ($request->isMethod('get')) {
            return view('domains.notification.create');
        }

        // Nếu là yêu cầu POST, xử lý tạo thông báo
        try {
            $service = CreateService::new($this->request, $this->auth);
            $result = $service->create();

            if ($result['success']) {
                $this->sessionMessage('success', $result['message']);
            } else {
                $this->sessionMessage('error', $result['message']);
            }

            return redirect()->route('notification.index');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (\Exception $e) {
            $this->sessionMessage('error', $e->getMessage());
            return redirect()->back()->withInput();
        }
    }
}