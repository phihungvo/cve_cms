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
                <span class="badge badge-danger ml-2 notification-badge"
                    style="display: {{ $unreadNotifications > 0 ? 'inline-block' : 'none' }}">
                    {{ $unreadNotifications > 99 ? '99+' : $unreadNotifications }}
                    <span class="visually-hidden">unread notifications</span>
                </span>
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

@push('styles')
    <style>
        .badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 75%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .badge-danger {
            color: #fff;
            background-color: #dc3545;
        }
    </style>
@endpush

@push('scripts')
    <script>
        function updateNotificationBadge() {
            fetch("{{ route('notification.unread-count') }}", {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        const badges = document.querySelectorAll('.notification-badge');
                        badges.forEach(badge => {
                            badge.innerText = data.data.unread_count > 99 ? '99+' : data.data.unread_count;
                            badge.style.display = data.data.unread_count > 0 ? 'inline-block' : 'none';
                        });
                    }
                })
                .catch(error => console.error('Error fetching unread count:', error));
        }

        // Cập nhật ngay khi tải trang
        document.addEventListener('DOMContentLoaded', updateNotificationBadge);
        // Cập nhật mỗi 30 giây
        setInterval(updateNotificationBadge, 30000);
    </script>
@endpush