@php
    try {
        $userId = \Illuminate\Support\Facades\Auth::id(); // Lấy ID của user hiện tại
        $userPermission = session('userPermission_' . $userId, []); // Lấy từ session, mặc định là mảng rỗng nếu không có
    } catch (\Exception $e) {
        $userPermission = [];
    }

    $allPermission = $userPermission['all'] ?? [];
@endphp

@extends('layouts.in')

@section('title', __('schedule-create.title'))

@section('body')
    <h2 class="text-lg font-medium mb-5">{{ __('Create New Schedule') }}</h2>

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

    <form method="POST" action="{{ route('schedule.create') }}">
        @csrf
        <!-- Schedule -->
        <div class="box p-5 mt-5">
            @if(isset($allPermission[App\Domains\User\Role\Enum\RoleEnum::ROOT->value]))
                <!-- Enterprise -->
                <div class="mb-4 p-2">
                    <label class="form-label">{{ __('Enterprise') }}</label>
                    <select name="enterprise_id" class="form-select" required>
                        @foreach($enterpriseOptions as $id => $name)
                            <option value="{{ $id }}" {{ old('enterperise_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
            <div class="p-2">
                <label for="name" class="form-label">{{ __('schedule-create.name') }}</label>
                <input type="text" name="name" class="form-control form-control-lg" id="name"
                       value="{{ old('name', $row->name ?? request()->input('name')) }}" required>
            </div>

            <div class="p-2">
                <label for="description" class="form-label">{{ __('schedule-create.description') }}</label>
                <input type="text" name="description" class="form-control form-control-lg" id="description"
                       value="{{ old('description', $row->description ?? request()->input('description')) }}"
                       required>
            </div>

            <!-- Playlist -->
            <div class="p-2">
                <label class="form-label">{{ __('Playlist') }}</label>
                <select name="playlist_id" class="form-select" id="playlist-select" required>
                    <option value="" disabled selected>{{ __('playlist-create.select-playlist') }}</option>
                    @foreach($playlistOptions as $id => $name)
                        <option value="{{ $id }}" {{ old('playlist_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Start Time (DateTime) -->
            <div class="p-2">
                <label class="form-label">{{ __('Start Time') }}</label>
                <input type="datetime-local" name="start_time" class="form-control" required
                       value="{{ old('start_time') }}">
            </div>

            <!-- End Time (DateTime) -->
            <div class="p-2">
                <label class="form-label">{{ __('End Time') }}</label>
                <input type="datetime-local" name="end_time" class="form-control" value="{{ old('end_time') }}">
            </div>

            <!-- Repeat -->
            <div class="p-2">
                <div class="form-check">
                    <input type="checkbox" name="repeat" value="1" class="form-check-switch" id="repeat-create"
                        {{ old('repeat') ? 'checked' : '' }}>
                    <label for="repeat-create" class="form-check-label">{{ __('Repeat') }}</label>
                </div>
            </div>
        </div>

        <div class="box p-5 mt-5 text-right">
            <button type="submit" class="btn btn-primary">
                {{ __('Create Schedule') }}
            </button>
            <a href="{{ route('schedule.index') }}" class="btn btn-secondary ml-2">
                {{ __('Cancel') }}
            </a>
        </div>

        <div class="box p-5 mt-5">
            <!-- Show Devices -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                {{--                                    @dd($devices)--}}
                @foreach($devices as $device)
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
                                        <label for="device-{{ $device['id'] }}"
                                               class="form-check-label">{{ __('Select Device') }}</label>
                                        <input type="checkbox" name="deviceIds[]" value="{{ $device['id'] }}"
                                               class="form-check-switch"
                                               id="device-{{ $device['id'] }}" checked
                                        >
                                    </div>
                                </div>
                                <p class="text-gray-500 mt-1 mb-2">{{ $device['model'] }}</p>
                                <div class="flex space-x-1">
                                    <!-- btn Push Playlist -->
                                    <a href="javascript:pushPlaylistToDevice({{$device->id}});"
                                       class="btn btn-primary">{{__('playlist-update.push-playlist-button')}}</a>
                                    <span>display id - {{$device->displays->first()->id ??'N/A'}}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </form>
@endsection
@push('scripts')
    <script>
        document.getElementById('playlist-select').addEventListener('change', function () {
            const playlistId = this.value;

            // Gửi request lên server
            fetch('{{route('display.by-playlist-id')}}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                },
                body: JSON.stringify({
                    playlist_id: playlistId
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status == 'success') {
                        console.log(data.data)
                        let devices = data.data;

                        // Lấy container hiển thị danh sách thiết bị
                        const deviceContainer = document.querySelector('.grid');

// Xóa nội dung cũ
                        deviceContainer.innerHTML = '';

// Render danh sách thiết bị
                        devices.forEach(item => {
                            console.log(item.device.id)
                            const deviceCard = `
        <div class="card mb-3 device rounded hover:scale-105" id="device-${item.device.id}" device-id="${item.device.id}">
            <div class="shadow-md rounded-lg overflow-hidden min-h-36">
                <div class="p-4">
                    <div class="flex justify-between items-center">
                        <h5 class="text-lg font-bold">${item.device.name}</h5>
                        <div class="flex justify-end items-center gap-1 mt-2">
                            <input type="checkbox" name="deviceIds[]" value="${item.device.id}" class="form-check-switch" id="device-${item.device.id}" checked>
                        </div>
                    </div>
                    <p class="text-gray-500 mt-1 mb-2">${item.device.model}</p>
                    <div class="flex justify-between items-center mt-2">
                        <a href="javascript:pushPlaylistToDevice(${item.device.id});" class="btn btn-primary">{{__('playlist-update.push-playlist-button')}}</a>
                        <span>display id - ${item.display.id ?? 'N/A'}</span>
                    </div>
                </div>
            </div>
        </div>
    `;
                            deviceContainer.insertAdjacentHTML('beforeend', deviceCard);
                        });
                    }
                    if (data.status == 'error') {
                        console.error(data.message)
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                })
        });
    </script>
@endpush
