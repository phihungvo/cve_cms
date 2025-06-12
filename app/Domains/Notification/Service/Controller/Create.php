<?php

declare(strict_types=1);

namespace App\Domains\Notification\Service\Controller;

use App\Domains\Notification\Action\Create as CreateAction;
use App\Domains\Notification\Model\Notification;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Role\Model\Role;
use App\Domains\User\Model\User;

class Create
{
    protected $request;
    protected $auth;

    public function __construct($request, ?User $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    public static function new($request, ?User $auth): self
    {
        return new self($request, $auth);
    }

    public function create(): array
    {
        // Kiểm tra quyền tạo thông báo
        if (!$this->auth->hasRole('root') && !$this->auth->isOwner() && !$this->auth->hasPermission('access-notification-create')) {
            throw new \Exception(__('notification-create.no-permission'));
        }

        // Lấy danh sách enterprise
        $enterprisesQuery = Enterprise::query();
        if (!$this->auth->hasRole('root')) {
            $enterprisesQuery->where('id', $this->auth->enterprise_id);
        }
        $enterprises = $enterprisesQuery->pluck('id')->toArray();

        // Lấy enterprise_id từ request, nếu không có thì mặc định là enterprise_id của owner
        $enterpriseId = $this->request->input('enterprise_id', $this->auth->isOwner() ? $this->auth->enterprise_id : null);

        // Lấy danh sách role dựa trên enterprise_id
        $rolesQuery = Role::query();
        if ($enterpriseId) {
            $rolesQuery->where('enterprise_id', $enterpriseId);
        }
        $roles = $rolesQuery->pluck('name')->toArray();

        // Lấy danh sách user dựa trên enterprise_id
        $usersQuery = User::query();
        if ($enterpriseId) {
            $usersQuery->where('enterprise_id', $enterpriseId);
        }
        $users = $usersQuery->pluck('id')->toArray();

        // Thêm giá trị 'all' vào danh sách roles nếu có enterprise_id
        if ($enterpriseId) {
            $roles[] = 'all';
        }

        // Validate dữ liệu
        $data = $this->request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'notification_type' => 'required|in:system,enterprise',
            'enterprise_id' => ['nullable', 'integer', 'in:' . implode(',', $enterprises)],
            'target_group' => 'nullable|in:' . implode(',', $roles),
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|in:' . implode(',', $users),
        ]);

        // Xử lý enterprise_id để tránh lỗi SQL khi chọn "None"
        if ($this->auth->hasRole('root') && empty($data['enterprise_id'])) {
            $data['enterprise_id'] = null;
        }

        // Đảm bảo enterprise_id được gán cho Owner hoặc người có quyền
        if (!$this->auth->hasRole('root')) {
            $data['enterprise_id'] = $this->auth->enterprise_id;
        }

        // Gọi action để tạo thông báo
        $action = new CreateAction();
        return $action->handle($data, $this->auth);
    }
}