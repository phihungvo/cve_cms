@php
    $hasChildren = !empty($menu['children'] ?? []);

    if (!function_exists('isAnyChildActive')) {
        function isAnyChildActive($children, $currentRoute, $currentUri)
        {
            foreach ($children as $child) {
                $childHasChildren = !empty($child['children'] ?? []);
                $uriPattern = str_replace('{id}', '[0-9]+', $child['menu_route_uri'] ?? '');
                $uriBase = $child['menu_route_uri'] ? explode('{', $child['menu_route_uri'])[0] : '';

                $isChildActive = ($child['menu_route_name'] && $currentRoute === $child['menu_route_name']) ||
                    ($uriPattern && preg_match("#^$uriPattern$#", $currentUri));

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

@push('scripts')
    <script>
        function updateNotificationBadge() {
            // Tìm thẻ side-menu__title chứa "Notification"
            const menuTitles = document.querySelectorAll('.side-menu__title');
            let notificationMenu = null;
            menuTitles.forEach(title => {
                if (title.textContent.trim().startsWith('Notification')) {
                    notificationMenu = title;
                }
            });

            if (!notificationMenu) return;

            // Xóa badge cũ nếu có
            const existingBadge = notificationMenu.querySelector('.notification-badge');
            if (existingBadge) existingBadge.remove();

            // Gọi API để lấy số thông báo chưa đọc
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
                        const unreadCount = data.data.unread_count;
                        if (unreadCount > 0) {
                            // Tạo badge mới
                            const badge = document.createElement('span');
                            badge.className = 'badge badge-danger ml-2 notification-badge';
                            badge.textContent = unreadCount > 99 ? '99+' : unreadCount;
                            badge.style.display = 'inline-block';
                            notificationMenu.insertBefore(badge, notificationMenu.querySelector('.side-menu__sub-icon'));
                        }
                    }
                })
                .catch(error => console.error('Error fetching unread count:', error));
        }

        // Thêm style cho badge
        const style = document.createElement('style');
        style.textContent = `
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
                    `;
        document.head.appendChild(style);

        // Cập nhật khi tải trang và mỗi 60 giây
        document.addEventListener('DOMContentLoaded', () => {
            updateNotificationBadge();
            setInterval(updateNotificationBadge, 60000);
        });
    </script>
@endpush