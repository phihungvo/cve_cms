<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controller;

use App\Domains\User\Role\Model\Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RolesByEnterpriseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $enterpriseId = $request->query('enterprise_id');

        if (!$enterpriseId) {
            return response()->json(['roles' => []], 200);
        }

        $roles = Role::where('enterprise_id', $enterpriseId)
            ->select('name')
            ->get();

        return response()->json(['roles' => $roles], 200);
    }
}