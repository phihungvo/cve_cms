<?php

declare(strict_types=1);

namespace App\Domains\Notification\Service\Controller;

use App\Domains\Notification\Action\Update as UpdateAction;
use App\Domains\Notification\Model\Notification;
use App\Domains\User\Role\Model\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Update
{
    protected Request $request;
    protected $auth;
    protected Notification $notification;

    public function __construct($request, $auth, Notification $notification)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->notification = $notification;
    }

    public static function new($request, $auth, Notification $notification): self
    {
        return new self($request, $auth, $notification);
    }

    public function data(): array
    {
        $enterpriseId = $this->auth->enterprise_id ?? null;
        $roles = Role::where('enterprise_id', $enterpriseId)
            ->orWhereNull('enterprise_id')
            ->pluck('name')
            ->toArray();

        return [
            'notification' => $this->notification,
            'roles' => $roles,
            'is_root' => $this->auth->hasRole('root'),
        ];
    }

    public function update(): Notification
    {
        // Xác thực dữ liệu
        $data = $this->request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'notification_type' => 'required|in:system,enterprise',
            'target_group' => 'nullable|string',
        ]);

        Log::info('Validated notification update data: ', $data);

        // Gọi action để cập nhật thông báo
        $action = new UpdateAction();
        return $action->handle($this->notification, $data, $this->auth);
    }
}