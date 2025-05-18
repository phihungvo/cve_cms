<nav class="side-nav">
    <ul>
        <li>
            <a href="{{ route('dashboard.index') }}"
                class="logo {{ request()->routeIs('dashboard.*') ? 'active' : '' }}"
                style="display: flex; justify-content: center; align-items: center; width: 200px; height: 50px;">
                <img src="{{ !empty($logo_url) && filter_var($logo_url, FILTER_VALIDATE_URL) ? $logo_url : '/build/images/logo.jpg' }}"
                    alt="Logo"
                    style="max-width: 100%; max-height: 100%; object-fit: contain;">
            </a>
        </li>

        @include ('layouts.molecules.in-sidebar-menu')
    </ul>
</nav>
