@php
    $hasChildren = !empty($menu['children'] ?? []);

    if (!function_exists('isAnyChildActive')) {
        function isAnyChildActive($children, $currentRoute, $currentUri)
        {
            foreach ($children as $child) {
                $childHasChildren = !empty($child['children'] ?? []);
                $uriPattern = str_replace('{id}', '[0-9]+', $child['menu_route_uri'] ?? '');
                $uriBase = $child['menu_route_uri'] ? explode('{', $child['menu_route_uri'])[0] : '';

                // Kiểm tra khớp chính xác route name hoặc URI pattern
                $isChildActive = ($child['menu_route_name'] && $currentRoute === $child['menu_route_name']) ||
                    ($uriPattern && preg_match("#^$uriPattern$#", $currentUri));

                // Nếu không khớp chính xác, kiểm tra URI base nhưng chỉ áp dụng cho child không có children
                if (!$isChildActive && $uriBase) {
                    $isChildActive = str_starts_with($currentUri, $uriBase) && !$childHasChildren;
                }

                if ($isChildActive) {
                    return true;
                }
                if ($childHasChildren && isAnyChildActive($child['children'], $currentRoute, $currentUri)) {
                    return true;
                }
            }
            return false;
        }
    }

    $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName();
    $currentUri = request()->path();

    // Logic cho $isActive
    $uriPattern = str_replace('{id}', '[0-9]+', $menu['menu_route_uri'] ?? '');

    $isActive = $hasChildren
        ? isAnyChildActive($menu['children'], $currentRoute, $currentUri)
        : (($menu['menu_route_name'] && $currentRoute === $menu['menu_route_name']) ||
            ($uriPattern && preg_match("#^$uriPattern$#", $currentUri)));

    $isOpenMenu = $hasChildren && $isActive;

    $hasParameters = strpos($menu['menu_route_uri'] ?? '', '{') !== false;
    $link = $hasChildren
        ? 'javascript:;'
        : ($menu['menu_route_name'] && \Illuminate\Support\Facades\Route::has($menu['menu_route_name']) && !$hasParameters
            ? route($menu['menu_route_name'])
            : '#');
@endphp

<li style="display: block;" data-menu-id="{{ $menu['menu_route_name'] ?? '' }}">
    <a href="{{ $link }}" class="side-menu {{ $isActive ? 'side-menu--active' : '' }}">
        <div class="side-menu__icon">@icon($menu['menu_icon'])</div>
        <div class="side-menu__title">
            {{ $menu['menu_name'] }}
            @if ($menu['menu_route_name'] === 'notification.index' && isset($unreadNotifications) && $unreadNotifications > 0)
                <span class="badge badge-danger ml-2 notification-badge">{{ $unreadNotifications }}</span>
            @endif
            @if ($hasChildren)
                <div class="side-menu__sub-icon {{ $isOpenMenu ? 'transform rotate-180' : '' }}"
                    style="transition: transform 0.3s ease;">@icon('chevron-down')</div>
            @endif
        </div>
    </a>

    @if ($hasChildren)
        <ul style="display: {{ $isOpenMenu ? 'block' : 'none' }}; background-color: transparent; margin-left: 20px;">
            @foreach ($menu['children'] as $subMenu)
                @include('partials.menu-item', ['menu' => $subMenu, 'ROUTE' => $currentRoute, 'unreadNotifications' => $unreadNotifications])
            @endforeach
        </ul>
    @endif
</li>