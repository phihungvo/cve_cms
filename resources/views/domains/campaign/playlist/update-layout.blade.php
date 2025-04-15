@extends('layouts.in')

@section('body')

    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            @yield('content')
        </div>
    </div>
@stop
