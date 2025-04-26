@extends('domains.device.rt-analytics-layout')
{{--@dd(get_defined_vars())--}}
@section('content-analytics')
    <div class="intro-y box p-5 mt-5">
        <h1 class="text-lg font-medium mb-5">Create Solution</h1>
        <div class="box mb-t p-5">
            <form method="POST">
                @csrf
                <input type="hidden" name="_action" value="create"/>
                {{--                @include('domains.solution.molecules.')--}}
                {{--                phan chunng--}}
                <div class="grid grid-cols-1 sm:grid-cols-1 lg:gap-4 sm:gap-0">
                    <div class="mt-2">
                        <!-- Name -->
                        <span class="text-red-500">*</span>
                        <label for="solution_name" class="form-label">Name</label>
                        <input type="text" name="solution_name" id="solution_name"
                               value="{{old('solution_name')}}"
                               class="form-control form-control-lg" required>
                    </div>

                    <div class="mt-2">
                        <!-- Description -->
                        <label for="description" class="form-label">Description</label>
                        <textarea name="description" id="description" class="form-control form-control-lg"
                                  style="resize: none" required>{{old('description')}}</textarea>
                    </div>
                </div>
{{--                end phan chung--}}
                <div class="box p-5 mt-5">
                    <div class="text-right">
                        <!--Btn Create Group -->
                        <input type="submit" class="btn btn-primary btn-sm" value="Create"/>
                        <!-- Btn Cancel -->
                        <a href="{{route('solution.index',['deviceId'=>$device->id])}}"
                           class="btn btn-outline-secondary btn-sm">Canel</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
