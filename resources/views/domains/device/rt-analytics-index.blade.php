@extends('domains.device.update-layout')

@section('content')
    <div class="intro-y box p-5 mt-5">
        <h2 class="text-lg font-medium mb-5">{{ __('rt-analytics-index.runtime-analytics') }}</h2>

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
        @if($row->enable_ai && $row->enabled)
            <!--  -->
            <div class="flex justify-between items-center p-2">
                <h2>{{ __('rt-analytics-index.all-instance-list') }}</h2>
                <a class="btn btn-primary" href="{{route('device.runtime-analytics.create', $row->id)}}">
                    {{__('rt-analytics-index.create-instance')}}
                </a>
            </div>

            {{--            table show list instance--}}
            <div class="overflow-auto scroll-visible header-sticky">
                <table id="device-cvedix-instance-list-table"
                       class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap"
                       data-table-sort
                       data-table-pagination data-table-pagination-limit="10">
                    <thead>
                    <tr>
                        <th class="text-center">{{ __('rt-analytics-index.no') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.instance-id') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.name') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.source') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.zones') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.lines') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.solutions') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.groups') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.update-at') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $index => $instance)
                        @php
                            $link = route('device.runtime-analytics.update', $row->id);
                            $updateAt = isset($instance->updated_at)
                        ? \Carbon\Carbon::parse($instance->updated_at)->setTimezone('Asia/Ho_Chi_Minh')
                        : '-'
                        @endphp
                        <tr>
                            <!-- No -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}" class="block">{{$index + 1}}</a></td>
                            <!-- ID -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}" class="block">{{$instance->id}}</a></td>
                            <!-- Name -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}" class="block">{{$instance->instance_name}}</a></td>
                            <!-- Source -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}" class="block">{{$instance->instance_source ?? '-'}}</a></td>
                            <!-- Zones -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}" class="block">{{$instance->instance_zones ?? '-'}}</a></td>
                            <!-- Lines -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}" class="block">{{$instance->instance_lines ?? '-'}}</a></td>
                            <!-- Solutions -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}" class="block">{{$instance->solution->solution_name ?? '-'}}</a></td>
                            <!-- Groups -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}" class="block">{{$instance->group->group_name ?? '-'}}</a></td>
                            <!-- Update At -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}" class="block">{{$updateAt}}</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <hr class="my-4">
        @else
            <div class="mb-4">
                <p class="text-sm text-gray-500">{{ __('rt-analytics-index.rt-analytics-not_supported') }}</p>
            </div>
        @endif
    </div>
@stop
