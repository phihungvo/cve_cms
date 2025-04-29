@extends('layouts.in')

@section('body')
    <form method="POST">
        <input type="hidden" name="_action" value="update"/>

        @include('domains.user-group.molecules.create-update')

        @php
            # Xác định xem group đã bị xóa mềm hay chưa
           $isDeleted = is_null($row->deleted_at);
        @endphp
        <div class="box p-5 mt-5">
            <div class="flex justify-end items-center">
                @if($isDeleted)
                    <!--Case chưa xóa mềm -->
                    <!-- btn Soft Delete -->
                    <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                       class="btn btn-outline-danger mr-2"
                    >{{__('Delete')}}</a>
                @else
                    <!--Case đã xóa mềm -->
                    <!-- btn Force Delete -->
                    <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                       class="btn btn-danger mr-2">{{__('force Delete')}}</a>

                    <!-- btn restore -->
                    <a href="javascript:;" data-toggle="modal" data-target="#restore-modal"
                       class="btn btn-outline-success mr-2">{{__('Restore')}}</a>

                @endif
                @if($isDeleted)
                   <!-- btn Save -->
                    <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                @endif
                <!-- btn cancel -->
                <a class="btn btn-secondary ml-2" href="{{route('group.index')}}">{{__('Cancel')}}</a>
            </div>
        </div>
    </form>

    @include('molecules.delete-modal',[
    'title' => $isDeleted ? __('Delete') : __('Force Delete'),
    'message' => $isDeleted ? __('Are you sure you want to delete this group?') : __('Are you sure you want to force delete this group?'),
    'action' => $isDeleted ? 'delete' : 'forceDelete'
    ])

    @include('molecules.restore-modal',[
    'title' => __('Restore Group'),
    'message' => __('Are you sure you want to restore this group?'),
    'action' => 'restore'
    ])
@endsection
