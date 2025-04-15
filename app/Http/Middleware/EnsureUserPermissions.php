<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Domains\User\Action\Set;

class EnsureUserPermissions
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            // Tạo instance của Set action với user hiện tại
            $action = app(Set::class, [
                'row' => Auth::user(),
                'request' => $request
            ]);
            $action->setUserEnterprise(); // Gọi setUserEnterprise để bind userPermission
            Log::info('EnsureUserPermissions executed', ['user_id' => Auth::user()->id]);
        } else {
            // Log::warning('No authenticated user found for EnsureUserPermissions');
        }

        return $next($request);
    }
}