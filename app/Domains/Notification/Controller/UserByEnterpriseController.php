<?php

declare(strict_types=1);

namespace App\Domains\Notification\Controller;

use App\Domains\User\Model\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserByEnterpriseController
{
    public function __invoke(Request $request): JsonResponse
    {
        $enterpriseId = $request->query('enterprise_id');

        // Kiểm tra enterprise_id
        if (!$enterpriseId) {
            return response()->json(['users' => []], 200);
        }

        // Lấy danh sách user thuộc enterprise_id
        $users = User::where('enterprise_id', $enterpriseId)
            ->select('id', 'name', 'email')
            ->get();

        return response()->json(['users' => $users], 200);
    }
}