@php use Illuminate\Support\Carbon; @endphp
@extends('domains.device.index-layout')

@section('content')
    <!-- Search Form -->
    <form method="GET" class="sm:flex sm:space-x-4">
        <div class="flex-grow mt-2 sm:mt-0 sm:space-x-4">
            <input type="search" name="search" class="form-control form-control-lg"
                   placeholder="{{__('Filter...')}}"
                   data-table-search="#device-group-list-table">
        </div>
        <!-- select Enterprise -->
        @if(auth()->user()->enterprise_id == null)
            <div class="sm:ml-4 mt-2 sm:mt-0">
                <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                          placeholder="{{__('user-group-index.select-enterprise')}}" data-change-submit></x-select>
            </div>
        @endif

        <!-- Btn Create -->
        <div class="sm:ml-4 mt-2 sm:mt-0">
            <a href="{{route('device_group.create')}}"
               class="btn form-control-lg bg-white">{{__('user-group-index.btn-create')}}</a>
        </div>
    </form>

    <!-- Data Table -->
    <div class="overflow-auto scroll-visible header-sticky mt-4">
        <table id="device-group-list-table"
               class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap"
               data-table-sort
               data-table-pagination data-table-pagination-limit="10"
        >
            <thead>
            <tr>
                <th class="w-1">{{__('user-group-index.no')}}</th>
                <th class="text-left w-1">{{__('user-group-index.name')}}</th>
                <th class="text-left w-1">{{__('user-group-index.description')}}</th>
                <th class="text-left w-1">{{__('user-group-index.enterprise')}}</th>
                <th class="text-left w-1">{{__('user-group-index.created-at')}}</th>
                <th class="text-left w-1">{{__('user-group-index.updated-at')}}</th>
            </tr>
            </thead>

            <tbody>
            @foreach($list as $index => $row)
                @php
                    $link = route('device_group.update', $row->id);
                    $createdAt = isset($row->created_at)
                        ? Carbon::parse($row->created_at)->setTimezone('Asia/Ho_Chi_Minh')->format('H:i, d/m/Y') : '';
                    $updatedAt = isset($row->updated_at)
                        ? Carbon::parse($row->updated_at)->setTimezone('Asia/Ho_Chi_Minh')->format('H:i, d/m/Y') :'';
                    $isDeleted = !is_null($row->deleted_at);
                @endphp
                <tr class="{{$isDeleted ? 'line-through' : ''}}">
                    <td><a href="{{$link}}">{{ $index + 1 }}</a></td> <!-- Số thứ tự đơn giản cho Collection -->
                    <td class="text-left"><a href="{{$link}}">{{ $row->name }}</a></td>
                    <td class="text-left"><a href="{{$link}}">{{ $row->description }}</a></td>
                    <td class="text-left"><a href="{{$link}}">{{ $row->enterprise->name ?? 'system_owner' }}</a></td>
                    <td class="text-left"><a href="{{$link}}">{{ $createdAt }}</a></td>
                    <td class="text-left"><a href="{{$link}}">{{ $updatedAt }}</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
@endsection
