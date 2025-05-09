@php use Carbon\Carbon; @endphp
@extends('domains.campaign.playlist.index-layout')

@section('content')
{{--    @dd($list) displays_count --}}
    <form method="get">
        <div class="sm:flex sm:space-x-4">
            <div class="flex-grow mt-2 sm:mt-0">
                <input type="search" class="form-control form-control-lg" placeholder="{{__('playlist-index.filter')}}" data-table-search="#playlist-list-table">
            </div>
            @if(auth()->user()->isRoleRoot())
                <div class="sm:ml-4 mt-2 sm:mt-0">
                    <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                              placeholder="{{__('playlist-index.enterprise')}}" data-change-submit></x-select>
                </div>
            @endif
            <!-- Btn Create -->
            <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
                <a href="{{ route('fpp.playlist.create') }}" class="btn form-control-lg whitespace-nowrap">{{__('playlist-index.create')}}</a>
            </div>
        </div>
    </form>
    <div class="overflow-auto scroll-visible header-sticky">
        <table id="playlist-list-table"
               class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap"
               data-table-sort data-table-pagination data-table-pagination-limit="10">
            <thead>
                <tr>
                    <th>{{__('playlist-index.no')}}</th>
                    <th>{{__('playlist-index.name')}}</th>
                    @if(auth()->user()->isRoleRoot())
                        <th>{{__('playlist-index.enterprise')}}</th>
                    @endif
                    <th>{{__('playlist-index.description')}}</th>
                    <th>{{__('playlist-index.count-display')}}</th>
                    <th>{{__('playlist-index.createAt')}}</th>
                    <th>{{__('playlist-index.updatedAt')}}</th>
                </tr>
            </thead>
            <tbody>
            @foreach($list as $index => $row)
                @php
                    $link = route('fpp.playlist.update', $row->id);
                    $createAt = isset($row -> created_at)
                    ? Carbon::parse($row->created_at)->setTimezone('Asia/Ho_Chi_Minh')->format('H:i, d/m/Y') : '';
                    $updateAt = isset($row -> updated_at)
                    ? Carbon::parse($row->updated_at)->setTimezone('Asia/Ho_Chi_Minh')->format('H:i, d/m/Y') :'';
                    $isDeleted = !is_null($row->deleted_at);
                @endphp

                <tr class="{{ $isDeleted ? 'line-through' : '' }}">
                    <td><a href="{{$link}}" class="block">{{$index + 1}}</a></td>
                    <td><a href="{{$link}}" class="block">{{$row->name}}</a></td>
                    @if(auth()->user()->isRoleRoot())
                        <td><a href="{{$link}}" class="block">{{$row->enterprise->name??''}}</a></td>
                    @endif
                    <td><a href="{{$link}}" class="block">{{$row->description}}</a></td>
                    <td><a href="{{$link}}" class="block">{{$row->displays_count}}</a></td>
                    <td><a href="{{$link}}" class="block">{{$createAt}}</a></td>
                    <td><a href="{{$link}}" class="block">{{$updateAt}}</a></td>
                </tr>

            @endforeach
            </tbody>

        </table>
    </div>

@endsection
