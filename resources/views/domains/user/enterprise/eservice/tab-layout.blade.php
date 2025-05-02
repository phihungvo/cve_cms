@extends ('layouts.in')

@section ('body')
<div class="box flex items-center px-5">
    <div class="flex flex-col">
        {{-- main nav --}}
        <div class="nav nav-tabs flex overflow-auto whitespace-nowrap" role="tablist">
            <!-- Device-->
            <a href="{{ route('user.enterprise.eservice.index') }}"
                class="p-4 {{ ($ROUTE === 'user.enterprise.eservice.index') ? 'active' : '' }}">{{ __('eservice-index.tab-name-service') }}</a>
            <!-- Device Status-->
            <a href="#" class="p-4" role="tab">{{ __('eservice-index.tab-name-license') }}</a>
            <!-- Device Log-->
            <a href="#" class="p-4" role="tab">{{ __('eservice-index.tab-name-billing') }}</a>
        </div>
        {{-- nav-inner --}}
        @yield('nav-inner')
    </div>

</div>

<div class="tab-content">
    <div class="tab-pane active" role="tabpanel">
        <div class="mt-5 mb-5">
        </div>

        @yield('content')
    </div>
</div>

@stop