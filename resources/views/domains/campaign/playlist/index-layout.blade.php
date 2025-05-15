@php use Illuminate\Support\Str; @endphp

@extends ('layouts.in')

@section ('body')
    <div class="box flex items-center px-5 mb-4">
        {{--    main nav --}}
        <div class="nav nav-tabs flex overflow-auto whitespace-nowrap" role="tablist">
            <!--List Playlist-->
            <a href="{{ route('fpp.playlist.index') }}"
               class="p-4
            {{ ($ROUTE === 'fpp.playlist.index') ? 'active' : '' }}
            {{Str::is('fpp.playlist*', $ROUTE) && !Str::is('playlist_group*', $ROUTE) ? 'active' : ''}}
            "
               role="tab">List Playlist</a>

            <!-- Playlist Groups-->
            <a href="{{ route('playlist_group.index') }}"
               class="p-4
           {{ (Illuminate\Support\Str::is('playlist_group*', $ROUTE)) ? 'active' : '' }}
{{--           {{ (Illuminate\Support\Str::is('group.*', $ROUTE)) ? 'active' : '' }}--}}
{{--           {{ (Illuminate\Support\Str::is('solution.*', $ROUTE)) ? 'active' : '' }}--}}
           "
               role="tab">{{ __('Playlist Groups') }}</a>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            @yield('content')
        </div>
    </div>

@stop
