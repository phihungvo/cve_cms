@extends('domains.device.update-layout')

@section('content')
    <div class="intro-y box p-5 mt-5">
        <h2 class="text-lg font-medium mb-5">{{ __('camera.runtime-analytics') }}</h2>

        <!-- Display Success or Error Messages -->
        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mb-4">
                {{ session('error') }}
            </div>
        @endif

        @if($row->enable_ai)
            <!--  -->
            <div class="flex justify-between items-center p-2">
                <h2>All Instance list</h2>
                <a class="btn btn-primary" href="{{route('device.runtime-analytics.create', $row->id)}}">Create Instance</a>
            </div>

{{--            table show list instance--}}
{{--        @dd($list)--}}
            <hr class="my-4">
        @else
            <div class="mb-4">
                <p class="text-sm text-gray-500">{{ __('camera.rt-analytics-not_supported') }}</p>
            </div>
        @endif
    </div>
@stop
