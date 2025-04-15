@extends('domains.device.update-layout')

@section('content')
    <div class="intro-y box p-5 mt-5">
        <h2 class="text-lg font-medium mb-5">{{ __('camera.camera-setting') }}</h2>

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

        @if($row->camera_supported)
            <!-- Create Camera Form -->
            <div class="box mb-5 p-5 ">
                <button class="flex align-center text-base font-medium py-2 cursor-pointer w-full rounded-md"
                        onclick="toggleForm('create-camera-form')">
                    <span class="flex-1 text-left">{{ __('camera.create') }}</span>

                    <svg class="w-5 h-5 transform transition-transform duration-200" id="path-icon" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="create-camera-form" style="display: {{!$row->cameras->isNotEmpty()? 'block' : 'none'}};" >
                    <form method="POST" action="{{ route('device.camera-setting.create', $row->id) }}">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:gap-4 sm:gap-0">
                            <!-- Name -->
                            <div>
                                <label class="form-label">{{ __('camera.name') }} <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="camera[name]" class="form-control"
                                       value="{{ old('camera.name') }}"
                                       required>
                            </div>
                            <!-- Description -->
                            <div>
                                <label class="form-label">{{ __('camera.description') }}</label>
                                <input type="text" name="camera[description]" class="form-control"
                                       value="{{ old('camera.description') }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:gap-4 sm:gap-0">
                            <!-- Model -->
                            <div>
                                <label class="form-label">{{ __('camera.model') }}</label>
                                <input type="text" name="camera[model]" class="form-control"
                                       value="{{ old('camera.model') }}">
                            </div>
                            <!-- Serial -->
                            <div>
                                <label class="form-label">{{ __('camera.serial') }} <span
                                        class="text-red-500">*</span></label>
                                <input type="text" name="camera[serial]" class="form-control"
                                       value="{{ old('camera.serial') }}"
                                       required>
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:gap-4 sm:gap-0">
                            <!-- URI -->
                            <div>
                                <label class="form-label">{{ __('camera.uri') }}</label>
                                <input type="text" name="camera[uri]" class="form-control"
                                       value="{{ old('camera.uri') }}">
                            </div>
                            <!-- Location -->
                            <div>
                                <label class="form-label">{{ __('camera.location') }}</label>
                                <input type="text" name="camera[location]" class="form-control"
                                       value="{{ old('camera.location') }}">
                            </div>
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Resolution -->
                            <div>
                                <label class="form-label">{{ __('camera.resolution') }}</label>
                                <input type="text" name="camera[resolution]" class="form-control"
                                       value="{{ old('camera.resolution') }}">
                            </div>
                            <!-- Submit and Cancel Buttons -->
                            <div class="grid items-end
                           lg:grid-rows-1 lg:grid-cols-4 lg:gap-x-4
                           sm:gap-y-4 sm:grid-rows-2 sm:grid-cols-1">
                                <button type="submit"
                                        class="btn btn-primary lg:py-2 sm:py-6">{{ __('camera.create') }}</button>
                                <a href="{{ route('device.update', $row->id) }}"
                                   class="btn btn-secondary lg:py-2 sm:py-6">{{ __('Cancel') }}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <hr class="my-4">

            <!-- Existing Cameras -->
            @if($row->cameras->isNotEmpty())
                @foreach($row->cameras as $camera)
                    <div class="intro-y box p-5 mt-5">
                        @php
                            $serverCamera = env('SERVER_CAMERA_URL'); // https://hls.aigova.com/ovacam/aigova/{serial}/{instance_id}/stream/render/index.m3u8
                            $hlsUrl = str_replace('{serial}', $camera->serial, $serverCamera);
                            $hlsUrl = str_replace('/{instance_id}', '', $hlsUrl);
                            $hlsUrl = str_replace('/render', '/orginal', $hlsUrl);
                        @endphp
                        <h3 class="text-base font-medium mb-3">{{ __('camera.camera_details') }}
                            - {{ $camera->name }}</h3>
                        <div class="grid items-start
                                lg:grid-cols-2 lg:grid-rows-1 lg:gap-x-4
                                sm:grid-cols-1 sm:grid-rows-2 sm:gap-y-4
                                ">
                            <!-- Video Stream (2/3 width) -->
                            @if($camera->uri)
                                <div class="">
                                    <h3 class="text-base font-medium mb-3">{{ __('camera.video_stream') }}</h3>
                                    <div class="card rounded">
                                        <video class="hls-video" width="100%" height="400" controls autoplay>
                                            <source src="{{ $hlsUrl }}" type="application/x-mpegURL">
                                            Your browser does not support the video tag.
                                        </video>
                                    </div>
                                </div>
                            @endif

                            <!-- Camera Details (1/3 width) -->
                            <div class="">
                                <form method="POST"
                                      action="{{ route('device.update.camera-setting.update', $row->id) }}">
                                    @csrf
                                    @method('PATCH')
                                    <div class="grid sm:grid-cols-1 lg:grid-cols-2 lg:gap-2 sm:gap-4">
                                        <!-- Name -->
                                        <div class="mb-4">
                                            <label class="form-label">{{ __('camera.name') }}</label>
                                            <input type="text" name="cameras[{{$camera->id}}][name]"
                                                   class="form-control"
                                                   value="{{ $camera->name }}">
                                        </div>
                                        <!-- Description -->
                                        <div class="mb-4">
                                            <label class="form-label">{{ __('camera.description') }}</label>
                                            <input type="text" name="cameras[{{$camera->id}}][description]"
                                                   class="form-control" value="{{ $camera->description }}">
                                        </div>
                                    </div>
                                    <div class="grid sm:grid-cols-1 lg:grid-cols-2 lg:gap-2 sm:gap-4">
                                        <!-- Model -->
                                        <div class="mb-4">
                                            <label class="form-label">{{ __('camera.model') }}</label>
                                            <input type="text" name="cameras[{{$camera->id}}][model]"
                                                   class="form-control"
                                                   value="{{ $camera->model }}">
                                        </div>
                                        <!-- Serial -->
                                        <div class="mb-4">
                                            <label class="form-label">{{ __('camera.serial') }}</label>
                                            <input type="text" name="cameras[{{$camera->id}}][serial]"
                                                   class="form-control"
                                                   value="{{ $camera->serial }}">
                                        </div>
                                    </div>
                                    <div class="grid sm:grid-cols-1 lg:grid-cols-2 lg:gap-2 sm:gap-4">
                                        <!-- URI -->
                                        <div class="mb-4">
                                            <label class="form-label">{{ __('camera.uri') }}</label>
                                            <input type="text" name="cameras[{{$camera->id}}][uri]" class="form-control"
                                                   value="{{ $camera->uri }}">
                                        </div>
                                        <!-- Location -->
                                        <div class="mb-4">
                                            <label class="form-label">{{ __('camera.location') }}</label>
                                            <input type="text" name="cameras[{{$camera->id}}][location]"
                                                   class="form-control" value="{{ $camera->location }}">
                                        </div>
                                    </div>
                                    <!-- Resolution -->
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                                        <div>
                                            <label class="form-label">{{ __('camera.resolution') }}</label>
                                            <input type="text" name="cameras[{{$camera->id}}][resolution]"
                                                   class="form-control" value="{{ $camera->resolution }}">
                                        </div>
                                    </div>
                                    <!-- Submit, Delete and Cancel Buttons -->
                                    <div class="grid lg:grid-cols-3 lg:grid-rows-1 sm:grid-cols-1 sm:grid-rows-2 gap-4">
                                        <button type="submit" name="action" value="updateCameras"
                                                class="btn btn-primary">{{ __('camera.update') }}</button>
                                        <a href="{{ route('device.update', $row->id) }}"
                                           class="btn btn-secondary">{{ __('Cancel') }}</a>
                                        <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                           onclick="document.getElementById('delete-camera-form').action = '{{ route('device.update.camera-setting.delete', [$row->id, $camera->id]) }}'; document.getElementById('delete-camera-name').innerText = '{{ addslashes($camera->name) }}';"
                                           class="btn btn-danger">{{ __('camera.delete') }}</a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    {{--                        <hr class="my-4">--}}
                @endforeach
            @else
                <div class="mb-4">
                    <p class="text-sm text-gray-500">{{ __('camera.no_cameras') }}</p>
                </div>
            @endif
        @else
            <div class="mb-4">
                <p class="text-sm text-gray-500">{{ __('camera.not_supported') }}</p>
            </div>
        @endif
    </div>

    <!-- Delete Modal -->
    @include('molecules.delete-modal', [
        'method' => 'delete',
        'route' => '#', // Placeholder, sẽ được cập nhật động qua JS
        'title' => __('camera.delete-modal.title', ['name' => '']),
        'message' => '<span id="delete-camera-name"></span>',
        'id' => 'delete-modal'
    ])
    </div>

@stop

@push('scripts')
    <!-- Thêm HLS.js từ CDN -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const deleteForm = document.querySelector('#delete-modal form');
            deleteForm.id = 'delete-camera-form'; // Đặt ID cho form để cập nhật action
        });


        /**
         *  Initialize HLS player for each video element
         */
        document.addEventListener('DOMContentLoaded', function () {
            <!-- Initialize HLS player for each video element -->
            const videos = document.querySelectorAll('.hls-video');

            videos.forEach((video, index) => {
                const hlsUrl = video.querySelector('source').src;

                if (Hls.isSupported()) {
                    const hls = new Hls();
                    hls.loadSource(hlsUrl);
                    hls.attachMedia(video);
                    hls.on(Hls.Events.ERROR, function (event, data) {
                        if (data.fatal) {
                            console.error(`HLS Error for video ${index}:`, data);
                        }
                    });
                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    video.src = hlsUrl;
                }
            });
        });

        /**
         * Toggle the visibility of the form
         */
        function toggleForm(formId) {
            const form = document.getElementById(formId);
            const icon = document.getElementById('path-icon');
            if (form.style.display === 'none') {
                form.style.display = 'block';
                icon.classList.add('rotate-180');
            } else {
                form.style.display = 'none';
                icon.classList.remove('rotate-180');
            }
        }

    </script>
@endpush
