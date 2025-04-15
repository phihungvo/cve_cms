<?php

declare(strict_types=1);

namespace App\Domains\User\Role\Service\Controller;

use App\Domains\User\Role\Model\Role as Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Index extends IndexMapAbstract
{
    /**
     * @var Request
     */
    protected Request $request;

    /**
     * @var mixed
     */
    protected mixed $auth;

    /**
     * @param Request $request
     * @param mixed $auth
     * @return void
     */
    public function __construct(Request $request, mixed $auth)
    {
        $this->request = $request;
        $this->auth = $auth;
    }

    /**
     * Create a new instance.
     *
     * @param Request $request
     * @param mixed $auth
     * @return static
     */
    public static function new(Request $request, mixed $auth): static
    {
        return new static($request, $auth);
    }

    /**
     * Get data for the index page.
     *
     * @return array
     */
    public function data(): array
    {
        $user = auth()->user();
        $roles = $this->list();

        // Lọc theo vai trò nếu cần
        $filteredRoles = $roles->filter(function ($role) use ($user) {
            if ($user->isRoleRoot()) {
                return true; // Root thấy tất cả
            }
            if ($user->isOwner()) {
                return true; // Owner thấy các role đã lọc bởi scopeByEnterprise
            }
            return false;
        });

        // Log::info('roles: ', $filteredRoles->toArray());

        return [
            'roles' => $filteredRoles,
            'search' => $this->request->get('search'),
        ];
    }

    /**
     * Get the list of roles as a Collection.
     *
     * @return \Illuminate\Support\Collection
     */
    protected function list(): Collection
    {
        $query = Model::query();

        // Nếu không phải root, áp dụng lọc enterprise_id
        if (!auth()->user()->isRoleRoot()) {
            $query->byEnterprise();
        }

        $query->select([
            'role.id',
            'role.name',
            'role.enterprise_id',
            'role.alias',
            'role.created_at',
            'role.deleted_at',
            'enterprise.name as enterprise_name',
        ])
            ->leftJoin('enterprise', 'role.enterprise_id', '=', 'enterprise.id')
            ->withTrashed();

        if ($this->request->filled('search')) {
            $search = $this->request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('role.name', 'LIKE', "%{$search}%")
                    ->orWhere('role.alias', 'LIKE', "%{$search}%");
            });
        }

        return $query->get()->map(function ($role) {
            return $this->formatRole($role);
        });
    }

    /**
     * Format role data, including feature names.
     *
     * @param \App\Domains\User\Role\Model\Role $role
     * @return array
     */
    public function formatRole(Model $role): array
    {
        return [
            'id' => $role->id,
            'name' => $role->name,
            'alias' => $role->alias,
            'created_at' => $role->created_at->toDateTimeString(),
            'deleted_at' => $role->deleted_at ? $role->deleted_at->toDateTimeString() : null,
            'enterprise_name' => $role->enterprise_name ?? 'N/A', // Thêm enterprise_name
        ];
    }
}
