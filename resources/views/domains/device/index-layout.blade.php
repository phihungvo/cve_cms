@php
    use Illuminate\Support\Str;
@endphp

@extends ('layouts.in')

@section ('body')
    <div class="box flex items-center px-5 mb-4">
        {{--    main nav --}}
        <div class="nav nav-tabs flex overflow-auto whitespace-nowrap" role="tablist">
            <!--List Device-->
            <a href="{{ route('device.index') }}"
            class="p-4
            {{ Str::is('device.*', $ROUTE) ? 'active' : '' }}
            {{ Str::is('solution.*', $ROUTE) ? 'active' : '' }}
            {{ Str::is('group.*', $ROUTE) ? 'active' : '' }}

            "
            role="tab">List Device</a>


            <!-- Device Groups-->
            <a href="{{ route('device_group.index') }}"
               class="p-4
           {{ (Illuminate\Support\Str::is('device_group*', $ROUTE)) ? 'active' : '' }}
{{--           {{ (Illuminate\Support\Str::is('group.*', $ROUTE)) ? 'active' : '' }}--}}
{{--           {{ (Illuminate\Support\Str::is('solution.*', $ROUTE)) ? 'active' : '' }}--}}
           "
               role="tab">{{ __('Device Groups') }}</a>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            @yield('content')
        </div>
    </div>

@stop
