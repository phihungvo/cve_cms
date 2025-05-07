@php use Illuminate\Support\Str; @endphp

@extends('layouts.in')

@section('body')
    <div class="flex items-center mb-4">
        <div class="box flex flex-col w-full mb-4">
            {{--    main nav --}}
            <div class="px-5 nav nav-tabs flex overflow-auto whitespace-nowrap"
                 role="tablist">
                <!--List Vehicle-->
                <a href="{{ route('vehicle.index') }}"
                   class="p-4
                {{ ($ROUTE === 'vehicle.index') ? 'active' : '' }}
                {{ (Str::is('vehicle*', $ROUTE) && !Str::is('vehicle_group*', $ROUTE)) ? 'active' : '' }}"
                   role="tab">List Vehicle</a>

                <!-- Vehicle Groups-->
                <a href="{{ route('vehicle_group.index') }}"
                   class="p-4
               {{ (Str::is('vehicle_group*', $ROUTE)) ? 'active' : '' }}
               "
                   role="tab">{{ __('Vehicle Groups') }}</a>
            </div>
            {{--     nav-inner --}}
            @yield('nav-inner')
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            @yield('content')
        </div>
    </div>
@endsection
