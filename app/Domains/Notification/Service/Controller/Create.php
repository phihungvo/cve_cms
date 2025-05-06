<?php

declare(strict_types=1);

namespace App\Domains\Notification\Service\Controller;

use App\Domains\Notification\Action\Create as CreateAction;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Role\Model\Role;
use App\Domains\User\Model\User;

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

    public function create(): array
    {
        // Lấy danh sách enterprise
        $enterprises = $this->auth->isRoot()
            ? Enterprise::all()->pluck('id')->toArray()
            : [$this->auth->enterprise_id];

        // Lấy enterprise_id từ request (hoặc enterprise_id của owner)
        $enterpriseId = $this->request->input('enterprise_id', $this->auth->enterprise_id);

        // Lấy danh sách role dựa trên enterprise_id
        $roles = Role::where('enterprise_id', $enterpriseId)->pluck('name')->toArray();

        // Lấy danh sách user dựa trên enterprise_id
        $users = User::where('enterprise_id', $enterpriseId)->pluck('id')->toArray();

        // Validate dữ liệu
        $data = $this->request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'notification_type' => 'required|in:system,enterprise',
            'enterprise_id' => [$this->auth->isOwner() ? 'required' : 'nullable', 'integer', 'in:' . implode(',', $enterprises)],
            'target_group' => 'nullable|in:' . implode(',', $roles),
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer|in:' . implode(',', $users),
        ]);

        // Nếu là owner, bắt buộc phải có enterprise_id
        if ($this->auth->isOwner() && empty($data['enterprise_id'])) {
            throw new \Exception(__('notification-create.owner-requires-enterprise'));
        }

        // Kiểm tra quyền root hoặc owner
        if (!$this->auth->isRoot() && !$this->auth->isOwner()) {
            throw new \Exception(__('notification-create.no-permission'));
        }

        // Gọi action để tạo thông báo
        $action = new CreateAction();
        return $action->handle($data, $this->auth);
    }
}