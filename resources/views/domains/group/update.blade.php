@extends('domains.device.rt-analytics-layout')

@section('content-analytics')
    <div class="intro-y box p-5 mt-5">
        <h1 class="text-lg font-medium mb-5">Update Group</h1>
        <div class="box mb-t p-5">
            <form method="POST">
                @csrf
                <input type="hidden" name="_action" value="update"/>
                <div class="grid grid-cols-1 sm:grid-cols-1 lg:gap-4 sm:gap-0">
                    <!-- Name -->
                    <div class="mt-2">
                        <span class="text-red-500">*</span>
                        <label for="group_name" class="form-label">{{__('group-create.name')}}</label>
                        <input type="text" name="group_name" id="group_name" class="form-control form-control-lg"
                               value="{{old('group_name', isset($row) ? $row->group_name : '')}}"
                               required>
                    </div>

                    <div class="mt-2">
                        <!-- Description -->
                        <label for="description" class="form-label">{{__('group-create.description')}}</label>
                        <textarea name="description" id="description" class="form-control form-control-lg"
                                  style="resize: none"
                                  required>{{old('description', isset($row) ? $row->description : '')}}</textarea>
                    </div>
                </div>
                <div class="box p-5 mt-5">
                    <div class="text-right">
                        <!--Btn Delete Group-->
                        <button type="button" class="btn btn-danger btn-sm"
                                data-toggle="modal"
                                data-target="#delete-modal">
                            Delete
                        </button>
                        <!--Btn Save Group -->
                        <input type="submit" class="btn btn-primary btn-sm" value="{{__('group-update.btn-update')}}"/>
                        <!-- Btn Cancel -->
                        <a href='{{route('group.index',['deviceId'=>$device->id])}}'
                           class="btn btn-outline-secondary btn-sm">{{__('group-update.btn-cancel')}}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('molecules.delete-modal',[
        'title' => __('group-update.modal-delete.message'),
        'message' => __('group-update.modal-delete.message')
    ])
@endsection
