@extends('layouts.in')

@section('body')
    <form method="POST">
        <input type="hidden" name="_action" value="update"/>
    <div class="box p-5">
        @include('domains.cvedixrt-instance.molecules.create-update')

        @php
            $isDeleted = is_null($row->deleted_at);
        @endphp


        <hr class="my-4">
        <!-- Tùy chọn nhập nguồn -->
        @php
            $selectSourceType = isset($row) && $camerasExisting->contains('uri', $row->source)
            ? 'selectExistingCamera'
            : 'input-source-text';
        @endphp
        <div>
            <span class="text-red-500">*</span>
            <label for="input-source-select"
                   class="form-label">{{ __('cvedixrt-instance-update.input-source-type') }}</label>
            <select class="form-control form-control-lg cursor-pointer" id="input-source-select">
                <option value="existing_camera" {{$selectSourceType == 'selectExistingCamera' ? 'selected' : ''}}>
                    {{ __('cvedixrt-instance-update.select-existing-camera') }}
                </option>
                <option value="add_input_source" {{$selectSourceType == 'input-source-text' ? 'selected' : ''}}>
                    {{ __('cvedixrt-instance-update.add-new-input-source') }}
                </option>
            </select>
        </div>

        <!-- Select existing camera -->
        <div id="select-existing-camera" style="display: none;" class="mt-2">
            <span class="text-red-500">*</span>
            <x-select name="uri" :options="$camerasExisting" value="uri" text="name"
                      id=""
                      :label="__('cvedixrt-instance-update.select-existing-camera')"
                      :placeholder="__('cvedixrt-instance-update.select-existing-camera-placeholder')"
                      :selected="old('source',isset($row) ? $row->source : null)"
                      required>
            </x-select>
        </div>

        <!-- Add input source -->
        <div id="enter-input-source" style="display: none;" class="mt-2">
            <span class="text-red-500">*</span>
            <label for="input-source-text" class="form-label">Input Source</label>
            <input class="form-control form-control-lg" type="text" name="input_source_text" id="input-source-text"
                   placeholder="Enter input source"
                   value="{{ isset($row) && $row->source !== 'camera->uri' ? $row->source : '' }}">
        </div>

        <!--Text Area Description -->
        <div class="mt-2">
            <label class="form-label">{{__('cvedixrt-instance-update.description')}}</label>
            <textarea class="w-full form-control form-control-lg" name="description" rows="4"
                      style="resize: none;"
            >{{old('description', isset($row) ?$row->description : '')}}
                            </textarea>
        </div>

        <div class="box p-5 mt-5">
            <div class="flex justify-end items-center">
                @if($isDeleted)
                    <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                       class="btn btn-outline-danger mr-2">{{ __('cvedixrt-instance-update.btn-delete') }}</a>
                @else
                    <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                       class="btn btn-danger mr-2">{{ __('cvedixrt-instance-update.btn-force-delete') }}</a>
                    <a href="javascript:;" data-toggle="modal" data-target="#restore-modal"
                       class="btn btn-outline-success mr-2">{{ __('cvedixrt-instance-update.btn-restore') }}</a>
                @endif

                @if($isDeleted)
                    <button type="submit" class="btn btn-primary">{{ __('cvedixrt-instance-update.btn-save') }}</button>
                @endif
                <a class="btn btn-secondary ml-2" href="{{ route('cvedixrt_instance.index') }}">{{ __('cvedixrt-instance-update.btn-cancel') }}</a>
            </div>
        </div>
    </div>
    </form>

    @include('molecules.delete-modal', [
        'title' => __('cvedixrt-instance-update.modal.delete.title'),
        'message' => __('cvedixrt-instance-update.modal.delete.title'),
        'action' => 'delete',
    ])

    @include('molecules.restore-modal', [
        'title' => __('Restore CvedixrtInstance'),
        'message' => __('Are you sure you want to restore this item?'),
        'action' => 'restore'
    ])
@endsection
@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const inputSourceSelect = document.getElementById('input-source-select');
            const selectExistingCamera = document.getElementById('select-existing-camera');
            const enterInputSource = document.getElementById('enter-input-source');

            function toggleInputSource() {
                const selectedValue = inputSourceSelect.value;
                console.log(`selectedValue: ${selectedValue}`);

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
