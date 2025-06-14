<!doctype html>
<html dir="{{ app('language')->rtl ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">

<head>
    @include('layouts.molecules.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @livewireStyles
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    @stack('styles')
</head>

<body class="main body-{{ str_replace('.', '-', $ROUTE) }} authenticated">
    @include('layouts.molecules.in-sidebar-mobile')

    <div class="wrapper">
        <div class="wrapper-box">
            @include('layouts.molecules.in-sidebar')

            <div class="content py-2 md:px-5 md:py-4"> <!-- Thu gọn padding content -->
                <x-message type="error" />
                <x-message type="success" />

                @yield('body')
            </div>
        </div>
    </div>

    @include('layouts.molecules.footer')
    @livewireScripts
    @stack('scripts')

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
        </script>
    @endpush
</body>

</html>
