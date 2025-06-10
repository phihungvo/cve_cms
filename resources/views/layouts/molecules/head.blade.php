    <meta charset="utf-8">

    <title>{!! Meta::get('title') !!}</title>

    <meta name="apple-mobile-web-app-status-bar-style" content="white-translucent">
    <meta name="apple-mobile-web-app-title" content="{{ config('app.name') }}" />
    <meta name="application-name" content="{{ config('app.name') }}" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="mobile-web-app-capable" content="yes" />
    <meta name="robots" content="none, noindex, nofollow, noarchive, nosnippet" />
    <meta name="theme-color" content="#FFFFFF">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="apple-touch-icon" type="image/png" href="@asset('build/images/webapp/logo.jpg')" />
    <link rel="apple-touch-startup-image" type="image/png" href="@asset('build/images/webapp/startup.jpg')" />
    <link rel="icon" href="@asset('build/images/webapp/logo.jpg')" type="image/png">
    <link rel="manifest" href="@asset('manifest.json')" defer="defer"/>
    <link rel="shortcut icon" type="image/png" href="@asset('build/images/webapp/logo.jpg')" />
    <link rel="stylesheet" href="@asset('build/css/main.min.css')" />

    {{--favico--}}
    <link rel="apple-touch-icon" sizes="57x57" href="@asset('/apple-icon-57x57.png')">
    <link rel="apple-touch-icon" sizes="60x60" href="@asset('/apple-icon-60x60.png')">
    <link rel="apple-touch-icon" sizes="72x72" href="@asset('/apple-icon-72x72.png')">
    <link rel="apple-touch-icon" sizes="76x76" href="@asset('/apple-icon-76x76.png')">
    <link rel="apple-touch-icon" sizes="114x114" href="@asset('/apple-icon-114x114.png')">
    <link rel="apple-touch-icon" sizes="120x120" href="@asset('/apple-icon-120x120.png')">
    <link rel="apple-touch-icon" sizes="144x144" href="@asset('/apple-icon-144x144.png')">
    <link rel="apple-touch-icon" sizes="152x152" href="@asset('/apple-icon-152x152.png')">
    <link rel="apple-touch-icon" sizes="180x180" href="@asset('/apple-icon-180x180.png')">
    <link rel="icon" type="image/png" sizes="192x192"  href="@asset('/android-icon-192x192.png')">
    <link rel="icon" type="image/png" sizes="32x32" href="@asset('/favicon-32x32.png')">
    <link rel="icon" type="image/png" sizes="96x96" href="@asset('/favicon-96x96.png')">
    <link rel="icon" type="image/png" sizes="16x16" href="@asset('/favicon-16x16.png')">
    {{--<link rel="manifest" href="/manifest.json">--}}
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">
    <script>
    const WWW = '{{ rtrim(asset('/'), '/') }}';
    </script>
