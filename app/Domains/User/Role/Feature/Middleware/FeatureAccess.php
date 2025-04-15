<?php declare(strict_types=1);

namespace App\Domains\User\Role\Feature\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException; // Import AuthorizationException
use App\Domains\User\Role\Feature\Model\Feature;
use App\Domains\User\Model\User;
use App\Domains\User\Model\UserRoles;
use App\Domains\User\Role\Model\Role;
use App\Domains\User\Role\Model\RoleFeatures;
use Illuminate\Support\Facades\Log;

class FeatureAccess
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @param string $permission
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $permission): mixed
    {
        $user = $request->user();

        if (!$user) {
            throw new AuthorizationException(__('middleware.user-not-authenticated'));
        }

        // if (!$this->hasPermission($user, $permission)) {
        //     throw new AuthorizationException(__('middleware.permission-denied', ['permission' => $permission]));
        // }

        $this->validateFeatureAccess($request, $permission);

        return $next($request);
    }

    /**
     * Kiểm tra quyền của user
     *
     * @param mixed $user
     * @param string $permission
     * @return bool
     */
    protected function hasPermission($user, string $permission): bool
    {
        return $user->hasPermission($permission);
    }

    /**
     * Validation bổ sung cho Feature
     *
     * @param \Illuminate\Http\Request $request
     * @param string $permission
     * @return void
     */
    protected function validateFeatureAccess(Request $request, string $permission): void
    {
        $routeName = $request->route()->getName();
        $user = $request->user();

        if ($routeName === 'user.role.feature.index' && !$this->canReadFeature($user, $request)) {
            throw new AuthorizationException(__('middleware.feature-read-denied'));
        }

        if ($routeName === 'user.role.feature.create' && !$this->canCreateFeature($user, $request)) {
            throw new AuthorizationException(__('middleware.feature-create-denied'));
        }

        if ($routeName === 'user.role.feature.update' && !$this->canUpdateFeature($user, (int) $request->route('id'), $request)) {
            throw new AuthorizationException(__('middleware.feature-update-denied'));
        }


        if ($routeName === 'user.role.feature.delete' && !$this->canDeleteFeature($user, (int) $request->route('id'), $request)) {
            throw new AuthorizationException(__('middleware.feature-delete-denied'));
        }
    }

    /**
     * Kiểm tra quyền đọc Feature
     *
     * @param \App\Domains\User\Model\User $user
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function canReadFeature(User $user, Request $request): bool
    {
        return $this->hasFeatureAccess($user, $request, 'features-list');
    }

    /**
     * Kiểm tra quyền tạo Feature
     *
     * @param \App\Domains\User\Model\User $user
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function canCreateFeature(User $user, Request $request): bool
    {
        // return $user->role === 'admin' || $user->role === 'manager';
        return $this->hasFeatureAccess($user, $request, 'features-create');

    }

    /**
     * Kiểm tra quyền cập nhật Feature
     *
     * @param \App\Domains\User\Model\User $user
     * @param int $featureId
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function canUpdateFeature(User $user, int $featureId, Request $request): bool
    {
        return $this->hasFeatureAccess($user, $request, 'features-update');
    }

    /**
     * Kiểm tra quyền xóa Feature
     *
     * @param \App\Domains\User\Model\User $user
     * @param int $featureId
     * @param \Illuminate\Http\Request $request
     * @return bool
     */
    protected function canDeleteFeature(User $user, int $featureId, Request $request): bool
    {
        // $feature = Feature::find($featureId);
        // return $feature && $user->role === 'admin';
        return $this->hasFeatureAccess($user, $request, 'features-delete');

    }

    protected function hasFeatureAccess(User $user, Request $request, string $featureAlias): bool
    {
        $feature = Feature::where('alias', $featureAlias)->first();
        if (!$feature) {
            return false;
        }
        $featureId = $feature->id;

        $userId = $request->input('userId') ?? $user->id;
        $userRecord = User::find($userId);
        if (!$userRecord) {
            return false;
        }

        // Log::info('User Record', [
        //     'userId' => $userRecord->id,
        //     'root' => $userRecord->root,
        //     'enterprise_id' => $userRecord->enterprise_id,
        // ]);

        if ($userRecord->root === 1) {
            return true;
        }

        $enterpriseId = $userRecord->enterprise_id;

        $userRoles = UserRoles::where('user_id', $userId)
            ->where('enterprise_id', $enterpriseId)
            ->pluck('id');

        if ($userRoles->isEmpty()) {
            return false;
        }

        foreach ($userRoles as $idUserRole) {
            // Log::info("Checking role_id: $idUserRole with feature_id: $featureId");
            if (RoleFeatures::where('role_id', $idUserRole)->where('feature_id', $featureId)->exists()) {
                return true;
            }
        }

        return false;
    }
}