@extends ('layouts.in')

@section ('body')
{{--    @dd(get_defined_vars())--}}
<div class="box flex items-center px-5">
    <div class="nav nav-tabs flex overflow-auto whitespace-nowrap" role="tablist">
        <!-- Device-->
        <a href="{{ route('device.update', $row->id) }}" class="p-4 {{ ($ROUTE === 'device.update') ? 'active' : '' }}"
            role="tab">{{ $row->name }}</a>
        <!-- Device Status-->
        <a href="{{ route('device.update.device-status', $row->id) }}"
            class="p-4 {{ ($ROUTE === 'device.update.device-status') ? 'active' : '' }}"
            role="tab">{{ __('device-update.device-status') }}</a>
        <!-- Device Log-->
        <a href="{{ route('device.update.device-log', $row->id) }}"
            class="p-4 {{ ($ROUTE === 'device.update.device-log') ? 'active' : '' }}"
            role="tab">{{ __('device-update.device-log') }}</a>
        <!-- Message-->
        <a href="{{ route('device.update.device-message', $row->id) }}"
            class="p-4 {{ ($ROUTE === 'device.update.device-message') ? 'active' : '' }}"
            role="tab">{{ __('device-update.messages') }}</a>
        <!-- Transfer-->
        @if ($AUTH->managerMode())
            <a href="{{ route('device.update.transfer', $row->id) }}"
               class="p-4 {{ ($ROUTE === 'device.update.transfer') ? 'active' : '' }}"
               role="tab">{{ __('device-update.transfer') }}</a>
        @endif
        <!-- Camera Settings-->
        <a href="{{ route('device.update.camera-setting', $row) }}"
            class="p-4 {{ ($ROUTE === 'device.update.camera-setting') ? 'active' : '' }}"
            role="tab">{{ __('camera.camera-setting') }}</a>
        <!-- CVEDIX-RT-Analytics-->
        <a href="{{ route('device.runtime-analytics', $row->id) }}"
           class="p-4 {{ (Illuminate\Support\Str::is('device.runtime-analytics*', $ROUTE)) ? 'active' : '' }}"
           role="tab">{{ __('rt-analytics-index.runtime-analytics') }}</a>
    </div>
</div>

<div class="tab-content">
    <div class="tab-pane active" role="tabpanel">
        @yield('content')
    </div>
</div>

@stop
