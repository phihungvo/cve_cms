@extends('domains.device.rt-analytics-layout')

@section('content-analytics')
    <div class="intro-y box p-5 mt-5">
        <h2 class="text-lg font-medium mb-5">List all groups</h2>
    <form method="get">
        <div class="sm:flex sm:space-x-4  justify-between items-center py-2">
            <!--Input search -->
            <div class="flex-grow mt-2 sm:mt-0">
                <input type="search" name="" id=""
                       class="form-control form-select-lg px-2"
                       placeholder="{{__('group-index.filter')}}"
                       data-table-search="#group-list-table">
            </div>

            <!--Btn create instance-->
            <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
                <a class="btn form-control-lg whitespace-nowrap"
                   href="{{route('group.create',['deviceId'=>$device->id])}}">
                    {{__('group-index.btn-create')}}
                </a>
            </div>
        </div>
    </form>
    </div>

    {{--    table show list Group--}}
    <div class="overflow-auto scroll-visible header-sticky">
        <table id="group-list-table"
               class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap"
               data-table-sort>
            <thead>
            <tr>
                <th class="text-center">{{ __('group-index.no') }}</th>
                <th class="text-center">{{ __('group-index.name') }}</th>
                <th class="text-center">{{ __('group-index.description') }}</th>
            </tr>
            </thead>
            <tbody>

            @foreach($list as $index => $group)
                @php
                    $links = route('group.update',['id' => $group->id, 'deviceId' => $device->id]);
                @endphp
                <tr>
                    <td class="text-center">
                        <a href="{{$links}}" class="block">{{ $index + 1 }}</a></td>
                    <td class="text-center">
                        <a href="{{$links}}" class="block"> {{ $group->group_name }}</a></td>
                    <td class="text-center">
                        <a href="{{$links}}" class="block">{{ $group->description }}</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
@endsection
