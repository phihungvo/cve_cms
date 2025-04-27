@extends('domains.device.rt-analytics-layout')

@section('content-analytics')
    <div class="intro-y box p-5 mt-5">
        <h1 class="text-lg font-medium mb-5">Update Solution</h1>
        <div class="box mb-t p-5">
            <form method="POST">
                @csrf
                <input type="hidden" name="_action" value="update"/>
                {{--                @include('domains.solution.molecules.')--}}
                {{--                phan chunng--}}
                <div class="grid grid-cols-1 sm:grid-cols-1 lg:gap-4 sm:gap-0">
                    <!-- Name -->
                    <div class="mt-2">
                        <span class="text-red-500">*</span>
                        <label for="solution_name" class="form-label">Name</label>
                        <input type="text" name="solution_name" id="solution_name" class="form-control form-control-lg"
                               value="{{old('solution_name', isset($row) ? $row->solution_name : '')}}"
                               required>
                    </div>

                    <div class="mt-2">
                        <!-- Description -->
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control form-control-lg"
                                  style="resize: none"
                                  required>{{old('description', isset($row) ? $row->description : '')}}</textarea>
                    </div>
                </div>
                {{--                end phan chung--}}
                <div class="box p-5 mt-5">
                    <div class="text-right">
                        <!--Btn Delete Group-->
                        <button type="button" class="btn btn-danger btn-sm"
                                data-toggle="modal"
                                data-target="#delete-modal">
                            {{__('solution-update.btn-delete')}}
                        </button>
                        <!--Btn Save Group -->
                        <input type="submit" class="btn btn-primary btn-sm" value="Update"/>
                        <!-- Btn Cancel -->
                        <a href="{{route('solution.index',['deviceId'=>$device->id])}}"
                           class="btn btn-outline-secondary btn-sm">Canel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @include('molecules.delete-modal',[
        'title' => __('solution-update.modal-delete.title'),
        'message' => __('solution-update.modal-delete.message')
])
@endsection
