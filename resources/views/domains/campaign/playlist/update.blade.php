@extends('domains.campaign.playlist.update-layout')

@section('content')
    {{--    @dd(get_defined_vars())--}}
    <form method="post">
        <input type="hidden" name="_action" value="update"/>
        @include('domains.campaign.playlist.molecules.create-update')
        @php
            # Xác định xem playlist đã bị xóa mềm hay chưa
                $isDeleted = is_null($row->deleted_at);
        @endphp

        <div class="box p-5 mt-5">
            <div class="flex justify-between items-center">
                @if($isDeleted)
                    @if(count($deviceIds) > 0)
                        <div>
                            <!-- Push Message Button-->
                            <a class="btn btn-success mr-2" href="javascript:void(0)"
                               onClick="pushMessage({{$row->id}})">{{__('playlist-update.push-all-message-button')}}</a>
                            <!-- Preview Message Button -->
                            <button class="btn btn-outline-secondary" type="button"
                                    onclick="previewMessage({{ $row->id }})">Preview Message
                            </button>
                        </div>
                    @else
                        <div class="placeholder:"></div>
                    @endif
                @else
                    <div class="placeholder"></div>
                @endif
                <div>
                    @if($isDeleted)
                        {{--                    Case chưa xóa mềm: show button Soft Delete--}}
                        <!-- btn Soft Delete -->
                        <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                           class="btn btn-outline-danger mr-2">{{__('playlist-update.soft-delete-button')}}</a>
                    @else
                        {{--                    Case đã bị xóa mềm: show button Force Delete--}}
                        <!-- btn Force Delete -->
                        <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                           class="btn btn-danger mr-2">{{__('playlist-update.force-delete-button')}}</a>
                        <!-- btn Restore -->
                        <a href="javascript:;" data-toggle="modal" data-target="#restore-modal"
                           class="btn btn-success mr-2">{{__('playlist-update.restore-button')}}</a>
                    @endif
                    @if($isDeleted)
                        <!-- btn Save -->
                        <button type="submit" class="btn btn-primary"
                                data-click-one>{{__('playlist-update.save')}}</button>

                    @endif
                    <!-- btn Cancel-->
                    <a href="{{route('fpp.playlist.index')}}"
                       class="btn btn-secondary ml-2">{{__('playlist-create.cancel')}}</a>
                </div>
            </div>
        </div>


        <div class="box p-5 mt-5">
            <!-- Show Devices -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($devices as $device)
                    @php
                        $selected = in_array($device['id'], $deviceIds)
                    @endphp
                    <div class="card mb-3 device
                        rounded hover:scale-105"
                         id="device-{{ $device['id'] }}" device-id="{{$device['id']}}"
                    >
                        <div class="shadow-md rounded-lg overflow-hidden min-h-36">
                            <div class="p-4">
                                <div class="flex justify-between items-center">
                                    <h5 class="text-lg font-bold">{{ $device['name'] }}</h5>
                                    <!-- Select Device to push message -->
                                    <div class="flex justify-end items-center gap-1 mt-2">
                                        <input type="checkbox" name="deviceIds[]" value="{{ $device['id'] }}"
                                               class="form-check-switch"
                                               id="device-{{ $device['id'] }}" {{ $selected ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <p class="text-gray-500 mt-1 mb-2">{{ $device['model'] }}</p>
                                @php
                                    $playlistPublished = $device->displays->first()->playlist_published ?? 0;
                                    $schedulePublished = $device->displays->first()->schedule_published ?? 0;
                                @endphp
                                @if($selected)
                                    <div class="flex justify-start items-center">
                                        @if($playlistPublished == 0 && $schedulePublished == 0)
                                            <a href="javascript:pushPlaylistToDevice({{ $device->id }}, '{{ $device->name }}');"
                                               class="btn btn-danger mr-1">
                                                {{ __('schedule-update.push-playlish-button')}}
                                            </a>
                                        @elseif($playlistPublished == 1 && $schedulePublished == 0)
                                            <a href="javascript:pushPlaylistToDevice({{ $device->id }}, '{{ $device->name }}');"
                                               class="btn btn-primary mr-1">
                                                {{ __('schedule-update.push-playlish-button') }}
                                            </a>
                                            <a href="javascript:void(0);"
                                               class="btn {{$row->schedules ? 'btn-danger' : 'btn-secondary cursor-default'}}"
                                               onclick="{{ $row->schedules ? 'pushScheduleToDevice(' . $device->id . ')' : '' }}">
                                                {{ __('schedule-update.push-schedule-button') }}
                                            </a>
                                        @elseif($playlistPublished == 1 && $schedulePublished == 1)
                                            <a href="javascript:pushPlaylistToDevice({{ $device->id }}, '{{ $device->name }}');"
                                               class="btn btn-primary mr-1">
                                                {{ __('schedule-update.push-playlish-button') }}
                                            </a>
                                            <a href="javascript:void(0);"
                                               class="btn {{$row->schedules ? 'btn-primary' : 'btn-secondary cursor-default'}}"
                                              onclick="{{ $row->schedules ? 'pushScheduleToDevice(' . $device->id . ', \'' . $device->name . '\')' : '' }}">
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
    </form>


    @include('molecules.delete-modal',[
    'title' => $isDeleted ? __('playlist-update.delete-title') : __('playlist_update.force-delete-title'),
    'message' => $isDeleted ? __('playlist-update.delete-message') : __('playlist_update.force-delete-message'),
    'action' => $isDeleted ? 'delete' : 'forceDelete'
    ])

    @include('molecules.restore-modal',[
        'title' => __('playlist-update.restore-title'),
        'message' => __('playlist-update.restore-message'),
        'action' => 'restore'
    ])

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/monaco-editor@0.43.0/min/vs/loader.js"></script>
    <script>
        /**
         * Push message to all devices
         *
         * @param playlistId
         */
        function pushMessage(playlistId) {
            Swal.fire({
                title: '{{__("playlist-update.push-message.title")}}',
                text: '{{__("playlist-update.push-message.text")}}',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#91C714',
                cancelButtonColor: '#888',
                confirmButtonText:  '{{__("playlist-update.push-message.confirm-button")}}',
                cancelButtonText:  '{{__("playlist-update.push-message.cancel-button")}}',
            }).then((result) => {
                if (result.isConfirmed) {
                    sendApiRequest(
                        "{{route('fpp.playlist.push-message')}}",
                        'POST',
                        {
                            playlist_id: playlistId,
                            _action : 'pushMessage'},
                        (data) => {
                            // Show modal success
                            showAlert('success','{{__("schedule-update.push-message.success")}}',null, data.data)
                        },
                        (errorMessage) => {
                            showAlert('error', "Error", errorMessage || '{{__("schedule-update.push-message.error-message")}}')
                        }
                    )
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
                text: `{{__("playlist-update.push-playlist-to-device.text")}} \"${deviceName}\"?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#142E71',
                cancelButtonColor: '#888',
                confirmButtonText: '{{__("playlist-update.push-playlist-to-device.confirm-button")}}',
                cancelButtonText: '{{__("playlist-update.push-playlist-to-device.cancel-button")}}',
            }).then((result)=>{
                if(result.isConfirmed){
                    sendApiRequest(
                        "{{route('fpp.playlist.push-message-to-devices')}}",
                        'POST',
                        {
                            playlist_id: {{$row->id}},
                            device_ids: [deviceId],
                            _action: 'pushMessageToDevices'
                        },
                        (data) => {
                            showAlert('success', '{{__("schedule-update.push-schedule-to-device.success")}}',null, data.data)
                        },
                        (errorMessage) => {
                            showAlert('error', 'Error', errorMessage || '{{__("schedule-update.push-schedule-to-device.error-message")}}');
                        }

                    )
                };
            });
        };

        /**
         * Push Schedule to device
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
                confirmButtonText: '{{__("schedule-update.push-schedule-to-device.confirm-button")}}`',
                cancelButtonText: '{{__("schedule-update.push-schedule-to-device.cancel-button")}}',
            }).then((result) => {
                if (result.isConfirmed) {
                    sendApiRequest(
                        "{{route('schedule.push-message-to-devices')}}",
                        'POST',
                        {
                            schedule_id: {{ $row->schedules->id ?? 0 }},
                            device_ids: [deviceId]
                        },
                        (data) => {
                            showAlert('success', '{{__("schedule-update.push-schedule-to-device.success")}}',null, data.data)
                        },
                        (errorMessage) => {
                            showAlert('error', 'Error', errorMessage || '{{__("schedule-update.push-schedule-to-device.error-message")}}');
                        }
                    )
                }
            });
        }

        /**
         * Preview message before pushing to device
         *
         * @param playlistId
         */
        function previewMessage(playlistId) {
            sendApiRequest(
                "{{route('playlist.preview-message')}}",
                'POST',
                {
                    playlist_id: playlistId,
                },
                (data) => {
                    showAlert('success', "{{__('playlist-update.preview-message.title')}}", null, data.data)
                },
                (errorMessage) => {
                    showAlert('error', 'Error', errorMessage || '{{__("playlist-update.preview-message.error-message")}}');
                }
            )
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
                confirmButtonText: '{{__("playlist-update.aler.confirm-button")}}',
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
                        onError(data.message || '{{__('playlist-update.aler.error-message')}}');
                    }
                })
                .catch((error) => {
                    console.error(error);
                    onError(error.message || '{{__("playlist-update.aler.error-message")}}');
                });
        }
    </script>
@endpush
