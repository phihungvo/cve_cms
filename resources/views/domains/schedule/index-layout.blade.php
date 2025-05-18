@php use Illuminate\Support\Str; @endphp

@extends ('layouts.in')

@section ('body')
    <div class="box flex items-center px-5 mb-4">
        {{--    main nav --}}
        <div class="nav nav-tabs flex overflow-auto whitespace-nowrap" role="tablist">
            <!--List Schedule-->
            <a href="{{ route('schedule.index') }}"
               class="p-4
            {{ ($ROUTE === 'schedule.index') ? 'active' : '' }}
            {{Str::is('schedule*', $ROUTE) && !Str::is('schedule_group*', $ROUTE) ? 'active' : ''}}
            "
               role="tab">{{__('schedule-index.list-schedule')}}</a>

            <!-- Schedule Groups-->
            <a href="{{ route('schedule_group.index') }}"
               class="p-4
           {{ (Illuminate\Support\Str::is('schedule_group*', $ROUTE)) ? 'active' : '' }}
           "
               role="tab">{{ __('schedule-index.schedule-group') }}</a>
        </div>
    </div>

    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            @yield('content')
        </div>
    </div>
@endsection
