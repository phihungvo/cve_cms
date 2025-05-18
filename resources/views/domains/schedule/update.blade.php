@extends('domains.schedule.index-layout')

@section('content')
    <div class="flex justify-between">
        <h2 class="box p-3 text-lg font-medium mb-5">{{ __('Update Schedule') }}</h2>
    </div>

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
    <form method="POST" action="{{ route('schedule.update', $schedule->id) }}">
        @csrf
        @method('PATCH')
        <div class="box p-5 mt5">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="name" class="form-label">{{ __('schedule-update.enterprise') }}</label>
                    <input class="form-control form-control-lg" type="text"
                           value="{{$schedule->enterprise->name ?? ''}}" disabled>
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <div class="mb-4">
                    <label for="name" class="form-label">{{ __('schedule-create.name') }}</label>
                    <input type="text" name="name" class="form-control form-control-lg" id="name"
                           value="{{ old('name', $schedule->name ?? request()->input('name')) }}" required>
                </div>

                <div class="mb-4">
                    <label for="description" class="form-label">{{ __('schedule-create.description') }}</label>
                    <input type="text" name="description" class="form-control form-control-lg" id="description"
                           value="{{ old('description', $schedule->description ?? request()->input('description')) }}"
                           required>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Start Time (DateTime) -->
                <div class="mb-4">
                    <label class="form-label">{{ __('Start Time') }}</label>
                    <input type="datetime-local" name="start_time" class="form-control" required
                           value="{{ old('start_time', $schedule->start_time ? $schedule->start_time->format('Y-m-d\TH:i') : '') }}">
                </div>

                <!-- End Time (DateTime) -->
                <div class="mb-4">
                    <label class="form-label">{{ __('End Time') }}</label>
                    <input type="datetime-local" name="end_time" class="form-control"
                           value="{{ old('end_time', $schedule->end_time ? $schedule->end_time->format('Y-m-d\TH:i') : '') }}">
                </div>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Playlist -->
                <div>
                    <label class="form-label">{{ __('Playlist') }}</label>
                    <select name="playlist_id" class="form-select" required>
                        @foreach($playlistOptions as $id => $name)
                            <option
                                value="{{ $id }}" {{ old('playlist_id', $schedule->playlist_id) == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <!-- Repeat -->
                <div class="flex justify-start items-end lg:mb-2">
                    <div class="form-check">
                        <input type="checkbox" name="repeat" value="1" class="form-check-switch"
                               id="repeat-{{ $schedule->id }}"
                            {{ old('repeat', $schedule->repeat) ? 'checked' : '' }}>
                        <label for="repeat-{{ $schedule->id }}" class="form-check-label">{{ __('Repeat') }}</label>
                    </div>
                </div>
            </div>
        </div>

        <div class="box p-5 mt-5">
            <!--       input Schedule Group-->
            <h3 class="text-lg font-bold mt-2">Schedule Group</h3>
            @if( isset( $scheduleGroups) && $scheduleGroups->count() <= 0)
                <p>No groups available</p>
            @endif
            @foreach($scheduleGroups as $item)
                <div class="p-2">
                    <div class="form-check">
                        <input type="checkbox" name="schedule_groups[]" value="{{$item->id}}" class="form-check-switch"
                               id="schedule-group-{{$item->id}}"
                            {{ isset($assignedScheduleGroups) && in_array($item->id, $assignedScheduleGroups->pluck('schedule_group_id')->toArray()) ? 'checked' : '' }}
                            {{ $REQUEST->input('schedule_groups') ? 'checked' : '' }}
                        >
                        <label for="schedule-group-{{$item->id}}" class="form-check-label">{{$item->name}}</label>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="box p-5 mt-5 ">
            <div class="flex justify-between items-center">
                @if(count($devices) !== 0)
                    <!-- Push Message Button -->
                    <div>
                        <a class="btn btn-success mr-2" href="javascript:void(0)"
                           onclick="pushMessage({{ $schedule->id }})">Push
                            Message</a>
                        <!-- Preview Message Button -->
                        <button class="btn btn-outline-secondary" type="button"
                                onclick="previewMessage({{ $schedule->id }})">Preview Message
                        </button>
                    </div>
                @else
                    <div class="placeholder"></div>
                @endif
                <div>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Update Schedule') }}
                    </button>
                    <a href="{{ route('schedule.index') }}" class="btn btn-secondary ml-2">
                        {{ __('Cancel') }}
                    </a>
                </div>

            </div>
        </div>

        @if(count($devices) !== 0)
            <div class="box p-5 mt-5">
                <!-- show devices -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($devices as $device)
                        <div class="card mb-3 device rounded hover:scale-105"
                             id="device-{{ $device['id'] }}"
                             device-id="{{$device['id']}}">
                            <div class="shadow-md rounded-lg overflow-hidden min-h-36 ">
                                <div class="p-4">
                                    <div class="flex justify-between items-center">
                                        <h5 class="text-lg font-bold">{{ $device['name'] }}</h5>
                                        <input type="checkbox" name="devicesIds[]" id="" value="{{$device->id}}"
                                               class="form-check-switch" checked>
                                    </div>
                                    <p class=" text-gray-500">
                                        {{ $device['model'] }}
                                    </p>
                                    @if($device->displays->isnotempty())
                                        <div class="flex justify-start items-center mt-2">
                                            @php
                                                $playlistPublished = $device->displays->first()->playlist_published ?? 0;
                                                $schedulePublished = $device->displays->first()->schedule_published ?? 0;
                                            @endphp

                                            @if($playlistPublished == 0 && $schedulePublished == 0)
                                                <a href="javascript:pushPlaylistToDevice({{ $device->id }},'{{$device->name}}');"
                                                   class="btn btn-danger mr-1">
                                                    {{ __('schedule-update.push-playlish-button')}}
                                                </a>
                                            @elseif($playlistPublished == 1 && $schedulePublished == 0)
                                                <a href="javascript:pushPlaylistToDevice({{ $device->id }});"
                                                   class="btn btn-primary mr-1">
                                                    {{ __('schedule-update.push-playlish-button') }}
                                                </a>
                                                <a href="javascript:pushScheduleToDevice({{ $device->id }}, '{{$schedule->name}}');"
                                                   class="btn btn-danger">
                                                    {{ __('schedule-update.push-schedule-button') }}
                                                </a>
                                            @elseif($playlistPublished == 1 && $schedulePublished == 1)
                                                <a href="javascript:pushPlaylistToDevice({{ $device->id }},'{{$device->name}}');"
                                                   class="btn btn-primary mr-1">
                                                    {{ __('schedule-update.push-playlish-button') }}
                                                </a>
                                                <a href="javascript:pushScheduleToDevice({{ $device->id }}, '{{$schedule->name}}');"
                                                   class="btn btn-primary">
                                                    {{ __('schedule-update.push-schedule-button') }}
                                                </a>
                                            @endif
                                        </div>
                                    @endif

                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </form>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.43.0/min/vs/loader.js"></script>

    <script>
        /**
         * Push message to all devices
         *
         * @param scheduleId
         */
        function pushMessage(scheduleId) {
            Swal.fire({
                title: '{{__("schedule-update.push-message.title")}}',
                text: '{{__("schedule-update.push-message.text")}}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#91C714',
                cancelButtonColor: '#888',
                confirmButtonText: '{{__("schedule-update.push-message.confirm-button")}}',
                cancelButtonText: '{{__("schedule-update.push-message.cancel-button")}}'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendApiRequest(
                        "{{ route('schedule.push-message') }}",
                        'POST',
                        { schedule_id: scheduleId },
                        (data) => {
                            showAlert('success', 'Push Message Successfully', null, data.data);
                        },
                        (errorMessage) => {
                            showAlert('error', 'Error', errorMessage || 'Failed to push message.');
                        }
                    );
                }
            });
        }

        /**
         * Push playlist to device
         *
         * @param deviceId
         * @param deviceName
         */
        function pushPlaylistToDevice(deviceId, deviceName) {
            Swal.fire({
                title: '{{__("playlist-update.push-playlist-to-device.title")}}',
                text: `{{__("playlist-update.push-playlist-to-device.text")}} ${deviceName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#142E71',
                cancelButtonColor: '#888',
                confirmButtonText: '{{__("playlist-update.push-playlist-to-device.confirm-button")}}',
                cancelButtonText: '{{__("playlist-update.push-playlist-to-device.cancel-button")}}'
            }).then((result) => {
                if (result.isConfirmed) {
                    sendApiRequest(
                        "{{route('fpp.playlist.push-message-to-devices')}}",
                        'POST',
                        {
                            playlist_id: {{$schedule->playlist->id}},
                            device_ids: [deviceId],
                            _action: 'pushMessageToDevices',
                        },
                        (data) => {
                            showAlert('success', 'Push Message Successfully', null, data.data);
                        },
                        (errorMessage) => {
                            showAlert('error', 'Error', errorMessage || 'Failed to push playlist to device.');
                        }
                    );
                }
            });
        }

        /**
         * Push schedule to device
         *
         * @param deviceId
         */
        function pushScheduleToDevice(deviceId, deviceName) {
            Swal.fire({
                title: '{{__("schedule-update.push-schedule-to-device.title")}}',
                text: `{{__("schedule-update.push-schedule-to-device.text")}} \"${deviceName}\"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#142E71',
                cancelButtonColor: '#888',
                confirmButtonText: '{{__("schedule-update.push-schedule-to-device.confirm-button")}}',
                cancelButtonText: '{{__("schedule-update.push-schedule-to-device.cancel-button")}}',
            }).then((result) => {
                if (result.isConfirmed) {
                    sendApiRequest(
                        "{{route('schedule.push-message-to-devices')}}",
                        'POST',
                        {
                            schedule_id: {{ $schedule->id }},
                            device_ids: [deviceId]
                        },
                        (data) => {
                            showAlert('success', 'Push Schedule Successfully', null, data.data);
                        },
                        (errorMessage) => {
                            showAlert('error', 'Error', errorMessage || 'Failed to push schedule');
                        }
                    );
                }
            });
        }

        /**
         * Preview message before pushing to device
         *
         * @param scheduleId
         */
       function previewMessage(scheduleId) {
           sendApiRequest(
               "{{route('schedule.preview-message')}}",
               "POST",
               { schedule_id: scheduleId },
               (data) => {
                   showAlert(
                       'success',
                       "Preview Message",
                       null,
                       data.data
                   );
               },
               (errorMessage) => {
                   showAlert(
                       'error',
                       "Error",
                       errorMessage || 'Failed to preview message'
                   );
               }
           );
       }

        /// Hàm để syntax highlight JSON (cho đẹp như IDE)
        function syntaxHighlight(json) {
            json = json.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
            return json.replace(/("(\\u[a-zA-Z0-9]{4}|\\[^u]|[^\\"])*"(\s*:)?|\b(true|false|null)\b|\d+)/g, function (match) {
                let cls = 'number';
                if (/^"/.test(match)) {
                    if (/:$/.test(match)) {
                        cls = 'key';
                    } else {
                        cls = 'string';
                    }
                } else if (/true|false/.test(match)) {
                    cls = 'boolean';
                } else if (/null/.test(match)) {
                    cls = 'null';
                }
                return `<span class="${cls}">${match}</span>`;
            });
        }

        /// Hàm tiện ích để hiển thị thông báo SweetAlert
        function showAlert(type, title, text, data = null) {
            const swalWithBoostrapButtons = Swal.mixin({
                customClass: {
                    confirmButton: type === 'success' ? 'btn btn-primary px-4' : 'btn btn-danger px-4',
                },
                buttonsStyling: false,
            });

            swalWithBoostrapButtons.fire({
                icon: type,
                title: title,
                text: text,
                html: data
                    ? `<pre style="
                text-align: left;
                font-family: 'Fira Code', monospace;
                background: #f5f5f5;
                border-radius: 8px;
                overflow-x: auto;
                white-space: pre;
                margin: 0;
                padding: 0;">
<code style="display: block; padding: 10px;">${syntaxHighlight(JSON.stringify(data, null, 2))}</code></pre>`
                    : null,
                showCloseButton: true,
                confirmButtonText: 'Ok',
                width: '60%',
            });
        }

        /// Hàm tiện ích để gửi request API
        function sendApiRequest(url, method, body, onSuccess, onError) {
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                },
                body: JSON.stringify(body),
            })
                .then((response) => response.json())
                .then((data) => {
                    if (data.status === 'success') {
                        onSuccess(data);
                    } else {
                        onError(data.message || 'An error occurred.');
                    }
                })
                .catch((error) => {
                    console.error(error);
                    onError(error.message || 'An error occurred.');
                });
        }
    </script>
@endpush
