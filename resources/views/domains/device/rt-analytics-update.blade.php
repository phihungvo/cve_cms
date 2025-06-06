@extends('domains.device.rt-analytics-layout')

@section('content-analytics')
    <div class="intro-y box p-5 mt-5">
        <div class="flex  justify-between items-center px-5">
            <h2 class="text-lg font-medium mb-5">{{ __('rt-analytics-create.update-instance') }}</h2>
            <a
                href="{{ route('device.runtime-analytics.analytcs-rules',['id' => $row->id,'instanceId' => $instance->id]) }}"
                class="inline-block text-primary p-2 font-bold">analytics rule</a>
        </div>

        <!-- Display Success or Error Messages -->
        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mb-4">
                {{ session('error') }}
            </div>
        @endif
        @if($row->enable_ai && $row->enabled)
            <!--  -->
            <div class="box mb-5 p-5">
                <div id="update-instance-form">
                    <form method="POST">
                        <input type="hidden" name="_action" value="update"/>
                        @csrf

                        {{--include phan chung cua 2 form create update--}}
                        @include('domains.device.molecules.create-update-instance')

                        <hr class="my-4">
                        <!-- Tùy chọn nhập nguồn -->
                        @php
                            $selectSourceType = isset($instance) && $camerasExisting->contains('uri', $instance->input_source) ? 'selectExistingCamera' : 'input-source-text';
//                            dd($instance->input_source);
//                            if($selectSourceType == 'selectExistingCamera'){
//                                $inputSource = ;
                        @endphp
                        <div>
                            <span class="text-red-500">*</span>
                            <label for="input-source-select" class="form-label">{{ __('rt-analytics-input-source.input-source-type') }}</label>
                            <select class="form-control form-control-lg cursor-pointer" id="input-source-select">
                                <option value="existing_camera" {{$selectSourceType == 'selectExistingCamera' ? 'selected' : ''}}>
                                    {{ __('rt-analytics-input-source.select-existing-camera') }}
                                </option>
                                <option value="add_input_source" {{$selectSourceType == 'input-source-text' ? 'selected' : ''}}>
                                    {{ __('rt-analytics-input-source.add_new_input_source') }}
                                </option>
                            </select>
                        </div>

                        <!-- Select existing camera -->
                        <div id="select-existing-camera" style="display: none;" class="mt-2">
                            <span class="text-red-500">*</span>
                            <x-select name="uri" :options="$camerasExisting" value="uri" text="name"
                                      id=""
                                      :label="__('rt-analytics-input-source.select-existing-camera')"
                                      :placeholder="__('rt-analytics-input-source.select-existing-camera-placeholder')"
                                      :selected="old('input_source',isset($instance) ? $instance->input_source :null)"
                                      required>
                            </x-select>
                        </div>

                        <!-- Add input source -->
                        <div id="enter-input-source" style="display: none;" class="mt-2">
                            <span class="text-red-500">*</span>
                            <label for="input-source-text" class="form-label">Input Source</label>
                            <input class="form-control form-control-lg" type="text" name="input_source_text" id="input-source-text"
                                   placeholder="Enter input source"
                                   value="{{ isset($instance) && $instance->input_source !== 'camera->uri' ? $instance->input_source : '' }}">
                        </div>

                        <!--Text Area Description -->
                        <div class="mt-2">
                            <label class="form-label">{{__('rt-analytics-input-source.description')}}</label>
                            <textarea class="w-full form-control form-control-lg" name="description" rows="4"
                                      style="resize: none;"
                            >{{old('description', isset($instance) ?$instance->description : '')}}
                            </textarea>
                        </div>

                        <div class="box  p-5 mt-5">
                            <div class="text-right">
{{--                                <!--Btn Apply -->--}}
{{--                                <a href="{{route('device.runtime-analytics.input-source',['id'=>$row->id])}}?instanceId={{$instance->id}}" class="btn btn-outline-success ml-2">--}}
{{--                                    {{__('rt-analytics-create.btn-apply')}}--}}
{{--                                </a>--}}
                                <!--Btn Delete -->
                                <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                   class="btn btn-danger mr-2"
                                >{{__('rt-analytics-create.delete')}}</a>
                                <!--Btn save -->
                                <button type="submit" class="btn btn-primary">{{__('rt-analytics-create.btn-save')}}</button>
                                <!--Btn cancel-->
                                <a href="{{ route('device.runtime-analytics', ['id' => $row->id]) }}"
                                   class="btn btn-outline-secondary ml-2">{{__('rt-analytics-create.btn-cancel')}}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <hr class="my-4">
        @else
            <div class="mb-4">
                <p class="text-sm text-gray-500">{{ __('rt-analytics-create.rt-analytics-not_supported') }}</p>
            </div>
        @endif
    </div>

    @include ('molecules.delete-modal', [
      'title' => __('rt-analytics-create.delete-modal.title'),
      'message' => __('rt-analytics-create.delete-modal.message'),
    ])

@stop
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputSourceSelect = document.getElementById('input-source-select');
            const selectExistingCamera = document.getElementById('select-existing-camera');
            const enterInputSource = document.getElementById('enter-input-source');

            function toggleInputSource() {
                const selectedValue = inputSourceSelect.value;

                if (selectedValue === 'existing_camera') {
                    selectExistingCamera.style.display = 'block';
                    selectExistingCamera.querySelectorAll('[name]').forEach(el => {
                        el.required = true;
                        el.disabled = false;
                    });
                    enterInputSource.style.display = 'none';
                    enterInputSource.querySelectorAll('[name]').forEach(el => {
                        el.required = false;
                        el.disabled = true;
                    });
                } else if (selectedValue === 'add_input_source') {
                    selectExistingCamera.style.display = 'none';
                    selectExistingCamera.querySelectorAll('[name]').forEach(el => {
                        el.required = false;
                        el.disabled = true;
                    });
                    enterInputSource.style.display = 'block';
                    enterInputSource.querySelectorAll('[name]').forEach(el => {
                        el.required = true;
                        el.disabled = false;
                    });
                }
            }

            // Initialize the display based on the current selection
            toggleInputSource();

            // Add event listener to handle changes
            inputSourceSelect.addEventListener('change', toggleInputSource);
        });
    </script>
@endpush
