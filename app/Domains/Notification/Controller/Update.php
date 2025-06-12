<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controller;

use App\Domains\Device\Model\Device;
use App\Domains\Notification\Action\Update as UpdateAction;
use App\Domains\Notification\Model\Notification;
use App\Domains\User\Role\Model\Role;
use App\Domains\User\Enterprise\Model\Enterprise;
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
        $isRoot = Auth::user()->hasRole('root');
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
            // Lấy danh sách enterprise
            $enterprisesQuery = Enterprise::query();
            if (!Auth::user()->hasRole('root')) {
                $enterprisesQuery->where('id', Auth::user()->enterprise_id);
            }
            $enterprises = $enterprisesQuery->pluck('id')->toArray();

            // Lấy danh sách role dựa trên enterprise_id
            $enterpriseId = $notification->enterprise_id;
            $rolesQuery = Role::query();
            if ($enterpriseId) {
                $rolesQuery->where('enterprise_id', $enterpriseId);
            }
            $roles = $rolesQuery->pluck('name')->toArray();
            if ($enterpriseId) {
                $roles[] = 'all';
            }

            // Validate dữ liệu
            $data = $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'notification_type' => 'required|in:system,enterprise',
                'enterprise_id' => ['nullable', 'integer', 'in:' . implode(',', $enterprises)],
                'target_group' => 'nullable|in:' . implode(',', $roles),
            ]);

            // Xử lý enterprise_id
            if (Auth::user()->hasRole('root') && empty($data['enterprise_id'])) {
                $data['enterprise_id'] = null;
            } elseif (!Auth::user()->hasRole('root')) {
                $data['enterprise_id'] = Auth::user()->enterprise_id;
            }

            $action = app(UpdateAction::class);
            $action->handle($notification, $data, Auth::user());

            return redirect()->route('notification.index')->with('success', __('notification-update.success'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            return back()->withErrors($e->validator)->withInput()->with('error', __('notification-update.validation-error'));
        } catch (\Exception $e) {
            return back()->with('error', __('notification-update.error') . ': ' . $e->getMessage())->withInput();
        }
    }

    protected function getRoles(?int $enterpriseId): array
    {
        $rolesQuery = Role::query();
        if ($enterpriseId) {
            $rolesQuery->where('enterprise_id', $enterpriseId);
        }
        $roles = $rolesQuery->pluck('name')->toArray();
        if ($enterpriseId) {
            $roles[] = 'all';
        }
        return $roles;
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