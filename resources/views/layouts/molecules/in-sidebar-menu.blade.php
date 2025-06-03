@php
try {
    $userId = \Illuminate\Support\Facades\Auth::id(); // Lấy ID của user hiện tại
    $userPermission = session('userPermission_' . $userId, []); // Lấy từ session
    // Đếm số lượng thông báo chưa đọc
    $unreadNotifications = \App\Domains\Notification\Model\UserNotification::where('user_id', $userId)
        ->whereNull('read_at')
        ->count();
} catch (\Exception $e) {
    $userPermission = [];
    $unreadNotifications = 0; // Đặt mặc định là 0 nếu có lỗi
}

$menuGroups = $userPermission['menu'] ?? [];
$allPermission = $userPermission['all'] ?? [];
@endphp

<ul style="display: block; background-color: transparent;">
    @foreach ($menuGroups as $groupName => $menus)
        @foreach ($menus as $menu)
            @include('partials.menu-item', [
            'menu' => $menu,
            'ROUTE' => $ROUTE,
            'unreadNotifications' => $unreadNotifications // Truyền số lượng thông báo chưa đọc
        ])
        @endforeach
    @endforeach

    @if (
    isset($allPermission[\App\Domains\User\Role\Enum\RoleEnum::ROOT->value]) ||
    isset($allPermission[\App\Domains\User\Role\Enum\RoleEnum::OWNER->value])
)
        <li>
            <a href="{{ route('configuration.index') }}"
                class="side-menu {{ str_starts_with($ROUTE, 'configuration.') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon">@icon('settings')</div>
                <div class="side-menu__title">{{ __('in-sidebar.configuration') }}</div>
            </a>
        </li>

        <li>
            <a href="{{ route('timezone.index') }}"
                class="side-menu {{ str_starts_with($ROUTE, 'timezone.') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon">@icon('globe')</div>
                <div class="side-menu__title">{{ __('in-sidebar.timezone') }}</div>
            </a>
        </li>

        <li>
            <a href="{{ route('profile.update') }}"
                class="side-menu {{ str_starts_with($ROUTE, 'profile.update') ? 'side-menu--active' : '' }}">
                <div class="side-menu__icon">@icon('user')</div>
                <div class="side-menu__title">{{ __('in-sidebar.profile') }}</div>
            </a>
        </li>
    @endif

    <li>
        <a href="{{ route('user.logout') }}" class="side-menu">
            <div class="side-menu__icon">@icon('toggle-right')</div>
            <div class="side-menu__title">{{ __('in-sidebar.logout') }}</div>
        </a>
    </li>
</ul>

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
                            .then(re            sponse => response.json())
                .then(data =            > {
                    if (data            .status === 'success') {
                                    const badge = document.querySelector('.notification-badge');
                                    if (badge) {
                                        badge.innerText = data.data.unread_count;
                                        badge.style.display = data.data.unread_count > 0 ? 'inline-block' : 'none';
                                    }
                    }
                            })
                            .catch(error => console.error('Error fetching unread count:', error));
                    }

                                // Cập nhật badge mỗi 30 giây
            setInterval(updateNotificationBadge, 30000);
        // Cập nhật ngay khi tải trang
        document.addEventListener('DOMContentLoaded', updateNotificationBadge);
    </script>
@endpush
