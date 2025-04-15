@extends('layouts.in')

@section('body')
    <div class="overflow-auto scroll-visible header-sticky">
        <table id="enterprise-list-table"
               class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap">
            <thead>
            <tr>
                <th class="w-1">{{__('enterprise-index.no')}}</th>
                <th class="w-1">{{__('enterprise-index.name')}}</th>
                <th class="w-1">{{__('enterprise-index.email')}}</th>
                <th class="w-1">{{__('enterprise-index.role')}}</th>
                <th class="w-1">{{__('enterprise-index.enterpriseName')}}</th>
                <th class="w-1">{{__('enterprise-index.action')}}</th>
            </tr>
            </thead>
            <tbody>

            @foreach($data as $index =>$row)
                <tr>
                    <td>{{$index+1}}</td>
                    <td>{{$row['user_name']}}</td>
                    <td>{{$row['email']}}</td>
                    <td>{{$row['roleName']}}</td>
                    <td>{{$row['name']}}</td>
                    <td>
                        @if($row['deleted_at'] == null)
                            <a class="btn btn-primary min-w-5"
                               href="{{ route('user.enterprise.show',['id'=> $row['id']]) }}">{{__('enterprise-index.edit-button')}}</a>
                            <a class="btn btn-outline-danger min-w-5"
                               href="{{route('user.enterprise.destroy',['id'=> $row['id']])}}"
                               onclick="event.preventDefault(); document.getElementById('inactive-form-{{$row['id']}}').submit();">{{__('enterprise-index.inactive_button')}}</a>
                            <form id="inactive-form-{{$row['id']}}" action="{{route('user.enterprise.destroy',['id'=> $row['id']])}}" method="POST" style="display: none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @else
                            <a class="btn btn-success min-w-5"
                                href="{{route('user.enterprise.restore',['id'=> $row['id']])}}"
                                onclick="event.preventDefault(); document.getElementById('restore-form-{{$row['id']}}').submit();">{{__('enterprise-index.restore-button')}}</a>
                                <form id="restore-form-{{$row['id']}}" action="{{route('user.enterprise.restore',['id'=> $row['id']])}}" method="POST" style="display: none;">
                                    @csrf
                                    @method('PATCH')
                                </form>
                                <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                   class="btn btn-danger min-w-5">{{__('enterprise-index.delete-button')}}</a>
                        @endif
                    </td>
                </tr>
            @endforeach

            </tbody>
        </table>
    </div>
    @include('molecules.delete-modal', [
        'route' => route('user.enterprise.force-delete', ['id' => $row['id']]),
        'title' => __('enterprise-index.delete-title'),
        'message' => __('enterprise-index.delete-message'),
        'method' => 'delete',
    ])
@stop
