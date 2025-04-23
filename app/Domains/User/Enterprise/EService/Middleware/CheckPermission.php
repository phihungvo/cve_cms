<?php

namespace App\Domains\User\Permission\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Auth\Access\AuthorizationException;

class CheckPermission
{
    private const EXCLUDED_PATHS = ['/', 'user/logout', 'user/auth', 'manifest.json'];

    private const ACTION_METHODS = [
        'list' => ['GET'],
        'show' => ['GET'],
        'update' => ['PATCH'],
        'delete' => ['DELETE'],
        'any' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
        'create' => ['POST'],
        'restore' => ['POST', 'PATCH'],
    ];

    public function handle(Request $request, Closure $next, string $action = ''): mixed
    {
        $currentPath = $request->path();
        $currentMethod = $request->method();

        if (in_array($currentPath, self::EXCLUDED_PATHS)) {
            Log::info("Skipping permission check for path: $currentPath");
            return $next($request);
        }

        $user = Auth::user();
        if (!$user) {
            Log::warning('No authenticated user found in CheckPermission middleware');

            return redirect()->guest('user/auth');
        }

        $userId = $user->id;
        $userPermission = session('userPermission_'.$userId, []);
        $allPermissions = $userPermission['all'] ?? [];
        // Log::info("allPermissions: ", [$allPermissions]);

        if (isset($allPermissions['root'])) {
            Log::info("User has 'root' role, granting full access.");

            return $next($request);
        }

        if ($this->isRouteAllowed($allPermissions, $currentPath, $currentMethod)) {
            Log::info("Access granted for path: $currentPath with method: $currentMethod");

            return $next($request);
        }

        Log::warning("Access denied for path: $currentPath with method: $currentMethod");
        throw new AuthorizationException('You do not have permission to access this route.');
    }

    private function isRouteAllowed(array $allPermissions, string $currentPath, string $currentMethod): bool
    {
        foreach ($allPermissions as $role => $permissions) {
            foreach ($permissions as $permission) {
                $menuRouteUri = $permission['menu_route_uri'] ?? '';
                $alias = $permission['alias'] ?? '';

                if (empty($menuRouteUri) || empty($alias)) {
                    continue;
                }

                if ($menuRouteUri == 'fpp/schedule/{id}/restore') {
                    Log::info('matchesRoute: ', [$this->matchesRoute($menuRouteUri, $currentPath)]);
                    Log::info('matchesMethod: ', [$this->matchesMethod($alias, $currentMethod)]);

                }

                if ($this->matchesRoute($menuRouteUri, $currentPath) && $this->matchesMethod($alias, $currentMethod)) {
                    Log::info("Permission matched: route=$menuRouteUri, alias=$alias, method=$currentMethod for path=$currentPath");

                    return true;
                }
            }
        }

        return false;
    }

    private function matchesRoute(string $menuRouteUri, string $currentPath): bool
    {
        // Chuẩn hóa route
        $menuRouteUri = str_replace('.', '/', $menuRouteUri);

        // Nếu không có placeholder, yêu cầu khớp chính xác
        if (strpos($menuRouteUri, '{') === false) {
            return $menuRouteUri === $currentPath;
        }

        // Tạo pattern từ menuRouteUri với các placeholder
        $pattern = preg_replace_callback(
            '/\{([^}]+)\}/',
            function ($matches) {
                $param = $matches[1];
                $isOptional = str_ends_with($param, '?');
                $paramName = $isOptional ? rtrim($param, '?') : $param;

                return match ($paramName) {
                    'id' => '(\d+)'.($isOptional ? '?' : ''),
                    'name', 'path', 'file', 'uuid', 'slug' => '([^/]+)'.($isOptional ? '?' : ''),
                    default => '([^/]+)'.($isOptional ? '?' : ''),
                };
            },
            $menuRouteUri
        );

        $pattern = '#^'.str_replace('/', '\/', $pattern).'$#';

        return preg_match($pattern, $currentPath) === 1;
    }

    private function matchesMethod(string $alias, string $currentMethod): bool
    {
        $segments = explode('-', $alias);
        $lastSegment = end($segments);

        return isset(self::ACTION_METHODS[$lastSegment]) && in_array($currentMethod, self::ACTION_METHODS[$lastSegment]);
    }
}
