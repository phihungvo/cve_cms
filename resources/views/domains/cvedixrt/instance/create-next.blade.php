@extends('layouts.in')

@section('body')
    <div class="intro-y box p-5 mt-5">
        <h2 class="text-lg font-medium mb-5">{{__('cvedixrt-instance-create.input-source')}}</h2>
    </div>

    <form method="POST">
        <input type="hidden" name="_action" value="create"/>
        @csrf
        <div class="box p-5">
        <!-- Form Body -->
        <div class="grid grid-cols-1 sm:grid-cols-1 lg:gap-4 sm:gap-0">
            <!-- Tùy chọn nhập nguồn -->
            <div>
                <span class="text-red-500">*</span>
                <label for="input-source-select" class="form-label">{{ __('rt-analytics-input-source.input-source-type') }}</label>
                <select name="" class="form-control form-control-lg" id="input-source-select">
                    <option
                        value="existing_camera">{{ __('rt-analytics-input-source.select-existing-camera') }}</option>
                    <option
                        value="add_input_source">{{ __('rt-analytics-input-source.add_new_input_source') }}</option>
                </select>
            </div>

            <!--Select existing camera -->
            <div id="select-existing-camera" style="display: none;" class="mt-2">
                <span class="text-red-500">*</span>
                <x-select name="uri" :options="$camerasExisting" value="uri" text="name"
                          id=""
                          :label="__('rt-analytics-input-source.select-existing-camera')"
                          :placeholder="__('rt-analytics-input-source.select-existing-camera-placeholder')"
                          required>
                </x-select>
            </div>

            <!--Add input source -->
            <div id="enter-input-source" style="display: none;" class="mt-2">
                <span class="text-red-500">*</span>
                <label for="input-source-text" class="form-label">Input Source</label>
                <input class="form-control form-control-lg" type="text" name="input_source_text" id="input-source-text" placeholder="Enter input source">
            </div>

            <!--Text Area Description -->
            <div class="mt-2">
                <label class="form-label">{{__('rt-analytics-input-source.description')}}</label>
                <textarea class="w-full form-control form-control-lg" name="description" rows="4" style="resize: none;"></textarea>
            </div>

            {{--            form footer--}}
            <div class="box p-5 mt-5 text-right">
                <!--Btn Save -->
                <button type="submit"
                        class="btn btn-primary ml-2">{{__('rt-analytics-input-source.btn-save')}}</button>
                <!--Btn cancel-->
                <a href="{{ route('cvedixrt_instance.index')}}"
                   class="btn btn-outline-secondary ml-2">{{__('rt-analytics-input-source.btn-cancel')}}</a>

            </div>
        </div>
        </div>
    </form>

@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const selectElement = document.getElementById('input-source-select');
            const existingCameraDiv = document.getElementById('select-existing-camera');
            const inputSourceDiv = document.getElementById('enter-input-source');
            const existingCameraInput = existingCameraDiv.querySelector('select[name="uri"]');
            const inputSourceTextInput = inputSourceDiv.querySelector('input[name="input_source_text"]');

            function toggleInputSource() {
                const selectedValue = selectElement.value;

                // Show/hide elements and enable/disable inputs
                if (selectedValue === 'existing_camera') {
                    existingCameraDiv.style.display = 'block';
                    inputSourceDiv.style.display = 'none';
                    existingCameraInput.disabled = false;
                    existingCameraInput.required = true;
                    inputSourceTextInput.disabled = true;
                    inputSourceTextInput.required = false;
                } else if (selectedValue === 'add_input_source') {
                    existingCameraDiv.style.display = 'none';
                    inputSourceDiv.style.display = 'block';
                    existingCameraInput.disabled = true;
                    existingCameraInput.required = false;
                    inputSourceTextInput.disabled = false;
                    inputSourceTextInput.required = true;
                } else {
                    existingCameraDiv.style.display = 'none';
                    inputSourceDiv.style.display = 'none';
                    existingCameraInput.disabled = true;
                    existingCameraInput.required = false;
                    inputSourceTextInput.disabled = true;
                    inputSourceTextInput.required = false;
                }
            }

            // Initialize the display based on the current selection
            toggleInputSource();

            // Add event listener to handle changes
            selectElement.addEventListener('change', toggleInputSource);
        });
    </script>
@endpush
