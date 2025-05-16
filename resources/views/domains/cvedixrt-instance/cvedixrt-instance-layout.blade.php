{{--@extends('domains.device.update-layout')--}}

@section('nav-inner')
    <div class="nav nav-tabs flex overflow-auto whitespace-nowrap" role="tablist">
        <!-- List all Instance-->
{{--        <a--}}
{{--            href="{{route('device.runtime-analytics', $row->id ?? $device->id)}}"--}}
{{--            class="p-4 {{($ROUTE === 'device.runtime-analytics') ? 'active': ''}}"--}}
{{--        >All Instance</a>--}}
        <a
{{--                href="{{route('device.runtime-analytics', $row->id ?? $device->id)}}"--}}
                class="p-4 {{($ROUTE === 'device.runtime-analytics') ? 'active': ''}}"
        >All Instance</a>
        <!-- List all Solution-->
        <a
{{--                href="{{route('solution.index')}}?deviceId={{ $row->id ?? $device->id }}"--}}
           class="p-4
{{--           {{($ROUTE === 'solution.index') ? 'active': ''}}--}}
           {{(Illuminate\Support\Str::is('solution.*', $ROUTE)) ? 'active': ''}}
           ">All Solution</a>
        <!-- List all Group-->
        <a
{{--                href="{{route('group.index')}}?deviceId={{ $row->id ?? $device->id}}"--}}
           class="p-4
{{--           {{($ROUTE === 'group.index') ? 'active': ''}}--}}
           {{(Illuminate\Support\Str::is('group.*', $ROUTE)) ? 'active': ''}}
           ">All Group</a>
    </div>
@endsection

@section('content')
    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            @yield('content-analytics')
        </div>
    </div>
@endsection
