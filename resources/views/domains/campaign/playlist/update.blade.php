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
                            <a class="btn btn-success" href="javascript:void(0)"
                               onClick="pushMessage({{$row->id}})">{{__('playlist-update.push-all-message-button')}}</a>
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
                                            <a href="javascript:pushPlaylistToDevice({{ $device->id }});"
                                               class="btn btn-danger mr-1">
                                                {{ __('schedule-update.push-playlish-button')}}
                                            </a>
                                        @elseif($playlistPublished == 1 && $schedulePublished == 0)
                                            <a href="javascript:pushPlaylistToDevice({{ $device->id }});"
                                               class="btn btn-primary mr-1">
                                            {{ __('schedule-update.push-playlish-button') }}
                                            </a>
                                            <a href="javascript:void(0);"
                                               class="btn {{$row->schedules ? 'btn-danger' : 'btn-secondary cursor-default'}}"
                                               onclick="{{ $row->schedules ? 'pushScheduleToDevice(' . $device->id . ')' : '' }}">
                                                {{ __('schedule-update.push-schedule-button') }}
                                            </a>
                                        @elseif($playlistPublished == 1 && $schedulePublished == 1)
                                            <a href="javascript:pushPlaylistToDevice({{ $device->id }});"
                                               class="btn btn-primary mr-1">
                                                {{ __('schedule-update.push-playlish-button') }}
                                            </a>
                                            <a href="javascript:void(0);"
                                               class="btn {{$row->schedules ? 'btn-primary' : 'btn-secondary cursor-default'}}"
                                               onclick="{{ $row->schedules ? 'pushScheduleToDevice(' . $device->id . ')' : '' }}">
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
    <script>
        function pushMessage(playlistId) {
            fetch("{{ route('fpp.playlist.push-message') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    playlist_id: playlistId,
                    _action: 'pushMessage',
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status == 'success') {
                        alert(data.data || 'Message pushed successfully');
                    }
                    if (data.status == 'error') {
                        alert(data.message || 'Failed to push message');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to push message');
                });
        }

        function pushPlaylistToDevice(deviceId) {
            alert('push message to device ' + deviceId);
            fetch("{{route('fpp.playlist.push-message-to-devices')}}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    playlist_id: {{$row->id}},
                    device_ids: [deviceId],
                    _action: 'pushMessageToDevices',
                })
            }).then(response => response.json())
                .then(data => {
                    if (data.status == 'success') {
                        alert(data.data || 'Playlist pushed successfully');
                        return;
                    }
                    if (data.status == 'error') {
                        alert(data.message || 'Failed to push playlist');
                        return;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to push playlist to device');
                })
        }

        function pushScheduleToDevice(deviceId) {
            fetch("{{route('schedule.push-message-to-devices')}}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    schedule_id: {{ $row->schedules->id ?? 0 }},
                    device_ids: [deviceId]
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status == 'success') {
                        alert(data.data || 'Schedule pushed successfully');
                    }
                    if (data.status == 'error') {
                        alert(data.message || 'Failed to push schedule');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to push schedule');
                });
        }
    </script>
@endpush
