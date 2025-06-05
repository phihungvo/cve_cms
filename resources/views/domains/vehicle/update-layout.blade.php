@extends ('domains.vehicle.index-layout')

@section('nav-inner')
    <div class="px-5 flex items-center">
        <div class="nav nav-tabs flex overflow-auto whitespace-nowrap" role="tablist">
            <a href="{{ route('vehicle.update', $row->id) }}" class="p-4 {{ $ROUTE === 'vehicle.update' ? 'active' : '' }}"
                role="tab">{{ $row->name }}</a>
            <a href="{{ route('vehicle.update.device', $row->id) }}"
                class="p-4 {{ $ROUTE === 'vehicle.update.device' ? 'active' : '' }}"
                role="tab">{{ __('vehicle-update.devices') }}</a>
            <a href="{{ route('vehicle.update.alarm', $row->id) }}"
                class="p-4 {{ $ROUTE === 'vehicle.update.alarm' ? 'active' : '' }}"
                role="tab">{{ __('vehicle-update.alarms') }}</a>
            <a href="{{ route('vehicle.update.alarm-notification', $row->id) }}"
                class="p-4 {{ $ROUTE === 'vehicle.update.alarm-notification' ? 'active' : '' }}"
                role="tab">{{ __('vehicle-update.notifications') }}</a>
            <a href="{{ route('vehicle.update.image-report', $row->id) }}"
                class="p-4 {{ $ROUTE === 'vehicle.update.image-report' ? 'active' : '' }}"
                role="tab">{{ __('vehicle-update.image-report') }}</a>
            <a href="{{ route('vehicle.update.campaign-report', $row->id) }}"
                class="p-4 {{ $ROUTE === 'vehicle.update.image-report' ? 'active' : '' }}"
                role="tab">{{ __('vehicle-update.campaign-report') }}</a>
        </div>
    </div>
@endsection

@section('content')
    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            @yield('content_inner')
        </div>
    </div>
@endsection
