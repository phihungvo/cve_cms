<?php declare(strict_types=1);

namespace App\Domains\User\Action;

use App\Domains\User\Role\Enum\RoleEnum;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Domains\User\Model\User as UserModel;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Role\Model\UserRole;
use App\Domains\User\Permission\Model\RolePermission;
use App\Domains\User\Permission\Model\Permission;
use App\Domains\User\Role\Model\Role;

class Set extends ActionAbstract
{
    /**
     * @return \App\Domains\User\Model\User
     */
    public function handle(): UserModel
    {
        $this->set();
        return $this->row;
    }

    /**
     * @return void
     */
    protected function set(): void
    {
        Log::info('Starting user set process', [
            'request_ip' => $this->request->ip(),
            'request_method' => $this->request->method(),
            'request_path' => $this->request->path(),
        ]);

        $this->setAuth();
        $this->setRow();
        $this->setLanguage();
    }

    /**
     * @return void
     */
    protected function setAuth(): void
    {
        Auth::login($this->row, true);

        Log::info('User authenticated', [
            'user_id' => $this->row->id,
            // 'user_email' => $this->row->email,
            'auth_method' => $this->row->email ?? $this->row->phone,
            'remember_me' => true,
        ]);
    }

    /**
     * @return void
     */
    protected function setRow(): void
    {
        app()->singleton('user', fn() => $this->row); // Sử dụng singleton

        Log::info('User bound to container', [
            'user_id' => $this->row->id,
            'user_name' => $this->row->name,
        ]);

        $this->setUserEnterprise();
    }

    /**
     * @return void
     */
    public function setUserEnterprise(): void
    {
        // Log::info('Starting setUserEnterprise process', ['user_id' => $this->row->id]);

        $user = UserModel::find($this->row->id);
        if (!$user) {
            Log::error('User not found', ['user_id' => $this->row->id]);
            return;
        }

        // Kiểm tra dữ liệu trong session trước
        $sessionKeyEnterprise = 'userEnterprise_' . $user->id;
        $sessionKeyPermission = 'userPermission_' . $user->id;

        $enterprise = session($sessionKeyEnterprise);
        $permissionsData = session($sessionKeyPermission);

        // Nếu đã có cả enterprise và permissions trong session thì không cần xử lý tiếp
        if ($enterprise && $permissionsData) {
            Log::info('Using existing enterprise and permissions from session', [
                'user_id' => $user->id,
                'enterprise_id' => $enterprise->id
            ]);
            return;
        }

        Log::info('User found', ['user_id' => $user->id, 'enterprise_id' => $user->enterprise_id]);

        if ($user->enterprise_id) {
            // Chỉ lấy enterprise nếu chưa có trong session
            if (!$enterprise) {
                $enterprise = Enterprise::find($user->enterprise_id);

                if ($enterprise) {
                    session([$sessionKeyEnterprise => $enterprise]);
                    // Log::info('UserEnterprise stored in session', [
                    //     'user_id' => $user->id,
                    //     'enterprise_id' => $enterprise->id,
                    //     'enterprise_name' => $enterprise->name,
                    // ]);
                } else {
                    // Log::warning('Enterprise not found for enterprise_id', [
                    //     'user_id' => $user->id,
                    //     'enterprise_id' => $user->enterprise_id,
                    // ]);
                    return;
                }
            }

            // Chỉ xử lý permissions nếu chưa có trong session
            if (!$permissionsData) {
                $userRoles = UserRole::where('user_id', $user->id)->pluck('role_id')->toArray();
                $roles = Role::whereIn('id', $userRoles)->pluck('alias', 'id');

                if ($roles->contains(RoleEnum::OWNER->value) || $roles->contains(RoleEnum::ROOT->value)) {
                    $roleKey = $roles->contains(RoleEnum::OWNER->value) ? RoleEnum::OWNER->value : RoleEnum::ROOT->value;

                    // Log::info('User is ' . $roleKey . ' with enterprise, retrieving all permissions', [
                    //     'user_id' => $user->id,
                    //     'role' => $roleKey
                    // ]);

                    $allPermissions = Permission::all()->keyBy('id');
                    $menuPermissions = Permission::where('is_menu', true)->get()->keyBy('id');

                    // Nếu là owner, lọc bỏ các permissions liên quan đến access-enterprise và access-permission
                    if ($roleKey === 'owner') {
                        $allPermissions = $allPermissions->filter(function ($permission) {
                            return !str_starts_with($permission->alias, 'access-enterprise') &&
                                !str_starts_with($permission->alias, 'access-permission');
                        });

                        $menuPermissions = $menuPermissions->filter(function ($permission) {
                            return !str_starts_with($permission->alias, 'access-enterprise') &&
                                !str_starts_with($permission->alias, 'access-permission');
                        });
                    }

                    $permissionsData = [
                        'user_id' => $user->id,
                        'menu' => [
                            $roleKey => $this->buildPermissionTree($menuPermissions),
                        ],
                        'all' => [
                            $roleKey => $this->buildAllPermissions($allPermissions),
                        ],
                    ];
                } else {
                    $this->setUserPermissions($user->id, $enterprise->id);
                    $permissionsData = session($sessionKeyPermission);
                }

                if ($permissionsData) {
                    session([$sessionKeyPermission => $permissionsData]);
                    // Log::info('UserPermission stored in session', [
                    //     'user_id' => $user->id,
                    //     'permissions' => $permissionsData,
                    // ]);
                }
            }
        } else {
            Log::info('No enterprise_id found, checking user roles', ['user_id' => $user->id]);

            // Chỉ xử lý nếu chưa có permissions trong session
            if (!$permissionsData) {
                $userRoles = UserRole::where('user_id', $user->id)->pluck('role_id')->toArray();

                if (empty($userRoles)) {
                    Log::warning('User has no roles', ['user_id' => $user->id]);
                    return;
                }

                $roles = Role::whereIn('id', $userRoles)->pluck('alias', 'id');

                if ($roles->contains(RoleEnum::ROOT->value)) {
                    Log::info('User is root, retrieving all permissions', ['user_id' => $user->id]);

                    $allPermissions = Permission::all()->keyBy('id');
                    $menuPermissions = Permission::where('is_menu', true)->get()->keyBy('id');

                    $permissionsData = [
                        'user_id' => $user->id,
                        'menu' => [
                            RoleEnum::ROOT->value => $this->buildPermissionTree($menuPermissions),
                        ],
                        'all' => [
                            RoleEnum::ROOT->value => $this->buildAllPermissions($allPermissions),
                        ],
                    ];

                    session([$sessionKeyPermission => $permissionsData]);
                    // Log::info('UserPermission stored in session', [
                    //     'user_id' => $user->id,
                    //     'permissions' => $permissionsData,
                    // ]);
                } else {
                    Log::warning('User is not root and has no enterprise_id', ['user_id' => $user->id]);
                }
            }
        }
    }

    /**
     * Xây dựng cây quyền cho menu
     */
    protected function buildPermissionTree($permissions): array
    {
        $tree = [];
        $rootPermissions = $permissions->filter(fn($permission) => is_null($permission->parent_id));
        $childPermissions = $permissions->filter(fn($permission) => !is_null($permission->parent_id));

        foreach ($rootPermissions as $permission) {
            $tree[$permission->id] = [
                'name' => $permission->name ?? 'Unnamed Name',
                'description' => $permission->description ?? 'Unnamed Permission',
                'alias' => $permission->alias,
                'menu_name' => $permission->menu_name,
                'menu_icon' => $permission->menu_icon,
                'children' => $this->getChildren($permission->id, $childPermissions),
                'menu_route_name' => $permission->menu_route_name,
                'menu_route_uri' => $permission->menu_route_uri,
            ];
        }

        return $tree;
    }

    /**
     * Lấy các quyền con
     */
    protected function getChildren($parentId, $permissions): array
    {
        $children = [];
        $childPermissions = $permissions->filter(fn($permission) => $permission->parent_id == $parentId);

        foreach ($childPermissions as $permission) {
            $children[$permission->id] = [
                'name' => $permission->name ?? 'Unnamed Name',
                'description' => $permission->description ?? 'Unnamed Permission',
                'alias' => $permission->alias,
                'menu_name' => $permission->menu_name,
                'menu_icon' => $permission->menu_icon,
                'children' => $this->getChildren($permission->id, $permissions),
                'menu_route_name' => $permission->menu_route_name,
                'menu_route_uri' => $permission->menu_route_uri,
            ];
        }

        return $children;
    }

    /**
     * Xây dựng tất cả quyền
     */
    protected function buildAllPermissions($permissions): array
    {
        return $permissions->mapWithKeys(function ($perm) {
            return [
                $perm->id => [
                    'id' => $perm->id,
                    'alias' => $perm->alias,
                    'name' => $perm->name,
                    'description' => $perm->description,
                    'parent_id' => $perm->parent_id,
                    'menu_route_name' => $perm->menu_route_name,
                    'menu_route_uri' => $perm->menu_route_uri,

                ]
            ];
        })->toArray();
    }

    /**
     * @param int $userId
     * @param int $enterpriseId
     * @return void
     */
    protected function setUserPermissions(int $userId, int $enterpriseId): void
    {
        $userRoles = UserRole::where('user_id', $userId)
            ->where('enterprise_id', $enterpriseId)
            ->get();

        if ($userRoles->isEmpty()) {
            // Log::warning('No roles found for user in enterprise', [
            //     'user_id' => $userId,
            //     'enterprise_id' => $enterpriseId,
            // ]);
            return;
        }

        $roleIds = $userRoles->pluck('role_id')->toArray();
        $roles = Role::whereIn('id', $roleIds)->get()->keyBy('id');
        $roleAliases = $roles->pluck('alias', 'id');

        $permissionIds = RolePermission::whereIn('role_id', $roleIds)
            ->pluck('permission_id')
            ->toArray();

        if (empty($permissionIds)) {
            // Log::warning('No permissions found for user roles', [
            //     'user_id' => $userId,
            //     'enterprise_id' => $enterpriseId,
            // ]);
            return;
        }

        $allPermissions = Permission::whereIn('id', $permissionIds)->get()->keyBy('id');
        $menuPermissions = Permission::whereIn('id', $permissionIds)
            ->where('is_menu', true)
            ->get()
            ->keyBy('id');

        $permissionsData = [
            'user_id' => $userId,
            'menu' => [],
            'all' => []
        ];

        foreach ($roleAliases as $roleId => $roleAlias) {
            $permissionsData['menu'][$roleAlias] = $this->buildPermissionTree($menuPermissions);
            $permissionsData['all'][$roleAlias] = $allPermissions->mapWithKeys(function ($perm) {
                return [
                    $perm->id => [
                        'id' => $perm->id,
                        'alias' => $perm->alias,
                        'name' => $perm->name,
                        'parent_id' => $perm->parent_id,
                        'menu_route_name' => $perm->menu_route_name,
                        'menu_route_uri' => $perm->menu_route_uri,
                    ]
                ];
            })->toArray();
        }

        // Log::info('Permissions Data', ['permissions' => $permissionsData]);

        if (!empty($permissionsData['menu']) || !empty($permissionsData['all'])) {
            // Lưu permissionsData vào session thay vì singleton
            session(['userPermission_' . $userId => $permissionsData]);
            // Log::info('UserPermission stored in session', [
            //     'user_id' => $userId,
            //     'permissions' => json_encode($permissionsData),
            // ]);
        } else {
            Log::warning('No permissions bound for user', [
                'user_id' => $userId,
                'enterprise_id' => $enterpriseId,
            ]);
            throw new AuthorizationException('Access denied.');
        }
    }

    /**
     * @return void
     */
    protected function setLanguage(): void
    {
        $this->factory('Language', $this->row->language)->action()->set();

        Log::info('User language set', [
            'user_id' => $this->row->id,
            'language' => $this->row->language?->code ?? 'default',
        ]);
    }
}
