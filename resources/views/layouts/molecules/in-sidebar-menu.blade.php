@php
    try {
        $userId = \Illuminate\Support\Facades\Auth::id(); // Lấy ID của user hiện tại
        $userPermission = session('userPermission_' . $userId, []); // Lấy từ session, mặc định là mảng rỗng nếu không có
    } catch (\Exception $e) {
        $userPermission = [];
    }

    $menuGroups = $userPermission['menu'] ?? [];
    $allPermission = $userPermission['all'] ?? [];
@endphp

<ul style="display: block; background-color: transparent;">
    @foreach ($menuGroups as $groupName => $menus)
        @foreach ($menus as $menu)
            @include('partials.menu-item', ['menu' => $menu, 'ROUTE' => $ROUTE])
        @endforeach
    @endforeach

    @if (isset($allPermission[App\Domains\User\Role\Enum\RoleEnum::ROOT->value]) ||
            isset($allPermission[App\Domains\User\Role\Enum\RoleEnum::OWNER->value]))
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

    {{-- Luôn hiển thị nút Logout --}}
    <li>
        <a href="{{ route('user.logout') }}" class="side-menu">
            <div class="side-menu__icon">@icon('toggle-right')</div>
            <div class="side-menu__title">{{ __('in-sidebar.logout') }}</div>
        </a>
    </li>
</ul>
