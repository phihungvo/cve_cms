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
                    >{{__('user-group-update.btn-delete')}}</a>
                @else
                    <!--Case đã xóa mềm -->
                    <!-- btn Force Delete -->
                    <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                       class="btn btn-danger mr-2">{{__('user-group-update.btn-force-delete')}}</a>

                    <!-- btn restore -->
                    <a href="javascript:;" data-toggle="modal" data-target="#restore-modal"
                       class="btn btn-outline-success mr-2">{{__('user-group-update.btn-restore')}}</a>

                @endif
                @if($isDeleted)
                   <!-- btn Save -->
                    <button type="submit" class="btn btn-primary">{{__('user-group-update.btn-save')}}</button>
                @endif
                <!-- btn cancel -->
                <a class="btn btn-secondary ml-2" href="{{route('group.index')}}">{{__('user-group-update.btn-cancel')}}</a>
            </div>
        </div>
    </form>

    @include('molecules.delete-modal',[
    'title' => $isDeleted ? __('user-group-update.modal.delete.title') : __('user-group-update.modal.force-delete.title'),
    'message' => $isDeleted ? __('user-group-update.modal.delete.message') : __('user-group-update.modal.force-delete.message'),
    'action' => $isDeleted ? 'delete' : 'forceDelete'
    ])

    @include('molecules.restore-modal',[
    'title' => __('user-group-update.modal.restore.title'),
    'message' => __('user-group-update.modal.restore.message'),
    'action' => 'restore'
    ])
@endsection
