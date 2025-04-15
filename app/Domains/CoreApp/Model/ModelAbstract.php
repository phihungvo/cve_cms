<?php declare(strict_types=1);

namespace App\Domains\CoreApp\Model;

use App\Domains\Core\Model\ModelAbstract as ModelAbstractCore;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Session;

abstract class ModelAbstract extends ModelAbstractCore
{
    /**
     * Local Scope để lọc theo enterprise_id từ session
     *
     * @param Builder $query
     *
     * @return Builder
     */
    public function scopeByEnterprise(Builder $query): Builder
    {
        $user = Auth::user();

        if ($user) {
            $userId = $user->id;

            // Kiểm tra dữ liệu trong session trước
            $sessionKeyEnterprise = 'userEnterprise_'.$userId;
            $sessionKeyPermission = 'userPermission_'.$userId;

            $enterprise = session($sessionKeyEnterprise);
            $permissionsData = session($sessionKeyPermission);

            // Nếu đã có cả enterprise và permissions trong session
            if ($enterprise && $permissionsData) {

                $enterpriseId = $enterprise->id;
            } else {
                $userPermission = session('userPermission_'.$userId, []);
                $allPermissions = $userPermission['all'] ?? [];

                // Kiểm tra nếu user có quyền root
                if (isset($allPermissions['root'])) {
                    // Trả về query không lọc enterprise_id nếu là root
                    return $query;
                }

                $enterprise = Session::get($sessionKeyEnterprise);
                $enterpriseId = is_object($enterprise) && property_exists($enterprise, 'id')
                    ? $enterprise->id
                    : config('app.default_enterprise_id', 1);
            }

            if (Schema::hasColumn($this->getTable(), 'enterprise_id')) {
                $query->where($this->getTable().'.enterprise_id', $enterpriseId);
            }
        }

        return $query;
    }
}
