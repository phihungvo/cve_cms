<!doctype html>
<html dir="{{ app('language')->rtl ? 'rtl' : 'ltr' }}" lang="{{ app()->getLocale() }}">

<head>
    @include ('layouts.molecules.head')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @livewireStyles
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
</head>

<body class="main body-{{ str_replace('.', '-', $ROUTE) }} authenticated">
    @include ('layouts.molecules.in-sidebar-mobile')

    <div class="wrapper">
        <div class="wrapper-box">
            @include ('layouts.molecules.in-sidebar')

            <div class="content py-5 md:px-10 md:py-8">
                <x-message type="error" />
                <x-message type="success" />

                @yield ('body')
            </div>
        </div>
    </div>

    @include ('layouts.molecules.footer')
    @livewireScripts
    @stack('scripts') <!-- Thêm dòng này -->
</body>

</html>