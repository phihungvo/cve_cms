@extends('domains.device.rt-analytics-layout')

@section('content-analytics')
    <div class="intro-y box p-5 mt-5">
        <h1 class="text-lg font-medium mb-5">Create Group</h1>
        <div class="box mb-t p-5">
            <form method="POST">
                @csrf
                <input type="hidden" name="_action" value="create"/>
                <div class="grid grid-cols-1 sm:grid-cols-1 lg:gap-4 sm:gap-0">
                    <div class="mt-2">
                        <!-- Name -->
                        <span class="text-red-500">*</span>
                        <label for="group_name" class="form-label">{{ __('group-index.name') }}</label>
                        <input type="text" name="group_name" id="group_name"
                               value="{{ old('group_name') }}"
                               class="form-control form-control-lg" required>
                    </div>

                    <div class="mt-2">
                        <!-- Description -->
                        <label for="description" class="form-label">{{ __('group-index.description') }}</label>
                        <textarea name="description" id="description" class="form-control form-control-lg"
                                  style="resize: none" required>{{old('description')}}</textarea>
                    </div>
                </div>
                <div class="box p-5 mt-5">
                    <div class="text-right">
                        <!--Btn Create Group -->
                        <input type="submit" class="btn btn-primary btn-sm" value="{{ __('group-index.btn-create') }}"/>
                        <!-- Btn Cancel -->
                        <a href="{{ route('group.index',['deviceId'=>$device->id])}}"
                           class="btn btn-outline-secondary btn-sm">{{ __('group-create.btn-cancel') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
