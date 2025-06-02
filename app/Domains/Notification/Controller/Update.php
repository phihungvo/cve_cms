<?php

namespace App\Domains\Notification\Controller;

use App\Domains\Device\Model\Device;
use App\Domains\Notification\Action\Update as UpdateAction;
use App\Domains\Notification\Model\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;

class Update extends ControllerAbstract
{
    public function __invoke(Request $request, $id)
    {
        // Tìm notification theo ID
        $notification = Notification::find($id);

        // Kiểm tra nếu notification không tồn tại
        if (!$notification) {
            return redirect()->route('notification.index')->with('error', __('notification-update.not-found'));
        }

        if ($request->isMethod('GET')) {
            return $this->showForm($notification);
        }

        return $this->updateNotification($request, $notification);
    }

    protected function showForm(Notification $notification)
    {
        $isRoot = Auth::user()->isRoot();
        $roles = $this->getRoles($notification->enterprise_id);
        $devices = $this->getDevices($notification);

        return view('domains.notification.update', [
            'notification' => $notification,
            'is_root' => $isRoot,
            'roles' => $roles,
            'devices' => $devices,
        ]);
    }

    protected function updateNotification(Request $request, Notification $notification)
    {
        try {
            $action = app(UpdateAction::class);
            $action->handle($notification, $request->all(), Auth::user());

            return redirect()->route('notification.index')->with('success', __('notification-update.success'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->errors())->withInput()->with('error', __('notification-update.validation-error'));
        } catch (\Exception $e) {
            return back()->with('error', __('notification-update.error') . ': ' . $e->getMessage())->withInput();
        }
    }

    protected function getRoles(?int $enterpriseId): array
    {
        // Thay bằng logic thực tế để lấy roles
        return ['User', 'Manager', 'Admin'];
    }

    protected function getDevices(Notification $notification)
    {
        $query = Device::query();

        if ($notification->enterprise_id) {
            $query->whereHas('displays', function ($q) use ($notification) {
                $q->where('enterprise_id', $notification->enterprise_id);
            });
        }

        if ($notification->userNotifications->isNotEmpty()) {
            $userIds = $notification->userNotifications->pluck('user_id')->toArray();
            $query->orWhereHas('displays', function ($q) use ($userIds) {
                $q->whereIn('user_id', $userIds);
            });
        }

        return $query->get();
    }
}