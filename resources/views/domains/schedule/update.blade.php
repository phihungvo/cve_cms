@extends('layouts.in')

@section('title', __('schedule-update.title'))

@section('body')
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

        <div class="box p-5 mt-5 ">
            <div class="flex justify-between items-center">
                @if(count($devices) !== 0)
                    <!-- Push Message Button -->
                    <div>
                        <a class="btn btn-success" href="javascript:void(0)" onclick="pushMessage({{ $schedule->id }})">Push
                            Message</a>
                    </div>
                @else
                    <div class="placeholder"></div>
                @endif
                    <button type="button" onclick="showToast()">showToast</button>
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
                                                <a href="javascript:pushPlaylistToDevice({{ $device->id }});" class="btn btn-danger mr-1">
                                                    {{ __('schedule-update.push-playlish-button')}}
                                                </a>
                                            @elseif($playlistPublished == 1 && $schedulePublished == 0)
                                                <a href="javascript:pushPlaylistToDevice({{ $device->id }});" class="btn btn-primary mr-1">
                                                    {{ __('schedule-update.push-playlish-button') }}
                                                </a>
                                                <a href="javascript:pushScheduleToDevice({{ $device->id }});" class="btn btn-danger">
                                                    {{ __('schedule-update.push-schedule-button') }}
                                                </a>
                                            @elseif($playlistPublished == 1 && $schedulePublished == 1)
                                                <a href="javascript:pushPlaylistToDevice({{ $device->id }});" class="btn btn-primary mr-1">
                                                    {{ __('schedule-update.push-playlish-button') }}
                                                </a>
                                                <a href="javascript:pushScheduleToDevice({{ $device->id }});" class="btn btn-primary">
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
    <script>
        function pushMessage(scheduleId) {
            fetch("{{ route('schedule.push-message') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    schedule_id: scheduleId
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
                    playlist_id: {{$schedule->playlist->id}},
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
                    schedule_id: {{ $schedule->id }},
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
