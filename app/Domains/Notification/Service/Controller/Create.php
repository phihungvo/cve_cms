<?php

declare(strict_types=1);

namespace App\Domains\Notification\Service\Controller;

use App\Domains\Notification\Action\Create as CreateAction;
use App\Domains\User\Role\Model\Role;
use Illuminate\Support\Facades\Log;

class Create
{
    protected $request;
    protected $auth;

    public function __construct($request, $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new($request, $auth): self
    {
        return new self($request, $auth);
    }

    public function create(): void
    {
        $enterpriseId = $this->auth->enterprise_id ?? null;

        // Xác thực dữ liệu
        $data = $this->request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'notification_type' => 'required|in:system,enterprise',
            'target_group' => 'nullable|string',
        ]);

        // Kiểm tra quyền tạo thông báo
        if ($data['notification_type'] === 'system' && !$this->auth->hasRole('root')) {
            throw new \Exception(__('notification-create.unauthorized-system'));
        }

        if ($data['notification_type'] === 'enterprise' && !$enterpriseId && !$this->auth->hasRole('root')) {
            throw new \Exception(__('notification-create.no-enterprise'));
        }

        // Nếu không phải root, gán enterprise_id của user
        if (!$this->auth->hasRole('root')) {
            $data['enterprise_id'] = $enterpriseId;
        } else {
            $data['enterprise_id'] = $data['notification_type'] === 'enterprise' ? $enterpriseId : null;
        }

        Log::info('Validated notification data: ', $data);

        // Gọi action để tạo thông báo
        $action = new CreateAction();
        $action->handle($data, $this->auth);
    }

    // Lấy danh sách vai trò để hiển thị trong form
    public function data(): array
    {
        $enterpriseId = $this->auth->enterprise_id ?? null;
        $roles = Role::where('enterprise_id', $enterpriseId)
            ->orWhereNull('enterprise_id')
            ->pluck('name')
            ->toArray();

        return [
            'roles' => $roles,
            'is_root' => $this->auth->hasRole('root'),
        ];
    }
}