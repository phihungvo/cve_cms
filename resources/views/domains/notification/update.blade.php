@php
    // Kiểm tra nếu $notification không tồn tại
    if (!isset($notification) || !$notification) {
        return redirect()->route('notification.index')->with('error', __('notification-update.not-found'));
    }
@endphp

@extends('layouts.in')

@section('title', __('notification-update.title'))

@section('body')
    <div class="intro-y box p-5">
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4 p-4">{{ session('error') }}</div>
        @endif

        <h2 class="text-lg font-medium mb-4">{{ __('notification-update.title') }}</h2>

        <form method="POST" action="{{ route('notification.update', $notification->id) }}" class="form">
            @csrf
            @method('PATCH')

            <!-- Tiêu đề -->
            <div class="mb-4">
                <label for="title" class="form-label">{{ __('notification-update.title-label') }}</label>
                <input type="text" name="title" id="title" class="form-control form-control-lg" required
                       placeholder="{{ __('notification-update.title-placeholder') }}"
                       value="{{ old('title', $notification->title) }}">
            </div>

            <!-- Nội dung -->
            <div class="mb-4">
                <label for="content" class="form-label">{{ __('notification-update.content-label') }}</label>
                <textarea name="content" id="content" class="form-control form-control-lg" required
                          placeholder="{{ __('notification-update.content-placeholder') }}">{{ old('content', $notification->content) }}</textarea>
            </div>

            <!-- Loại thông báo -->
            <div class="mb-4">
                <label for="notification_type" class="form-label">{{ __('notification-update.type-label') }}</label>
                <select name="notification_type" id="notification_type" class="form-control form-control-lg" required>
                    @if($is_root)
                        <option value="system" {{ old('notification_type', $notification->notification_type) === 'system' ? 'selected' : '' }}>
                            {{ __('notification-update.type-system') }}
                        </option>
                    @endif
                    <option value="enterprise" {{ old('notification_type', $notification->notification_type) === 'enterprise' ? 'selected' : '' }}>
                        {{ __('notification-update.type-enterprise') }}
                    </option>
                </select>
            </div>

            <!-- Nhóm mục tiêu -->
            <div class="mb-4">
                <label for="target_group" class="form-label">{{ __('notification-update.target-group-label') }}</label>
                <select name="target_group" id="target_group" class="form-control form-control-lg">
                    <option value="">{{ __('notification-update.target-group-all') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ old('target_group', $notification->target_group) === $role ? 'selected' : '' }}>
                            {{ $role }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Nút submit -->
            <div class="mt-6 flex justify-between">
                <div>
                    <button type="submit" class="btn btn-primary">{{ __('notification-update.submit') }}</button>
                    <a href="{{ route('notification.index') }}" class="btn btn-secondary ml-2">
                        {{ __('notification-update.cancel') }}
                    </a>
                </div>
                @if(auth()->check() && (auth()->user()->isRoot() || auth()->user()->isOwner()))
                    <div>
                        <a href="javascript:void(0)" onclick="pushMessage({{ $notification->id }})" class="btn btn-success">
                            {{ __('notification.push-message') }}
                        </a>
                    </div>
                @endif
            </div>
        </form>
    </div>

    <!-- Devices Section -->
    @if(auth()->check() && (auth()->user()->isRoot() || auth()->user()->isOwner()))
        <div class="box p-5 mt-5">
            <h2 class="text-lg font-medium mb-4">{{ __('notification-update.devices') }}</h2>
            @if($devices->isNotEmpty())
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($devices as $device)
                        <div class="card mb-3 device rounded hover:scale-105" id="device-{{ $device->id }}" device-id="{{ $device->id }}">
                            <div class="shadow-md rounded-lg overflow-hidden min-h-36">
                                <div class="p-4">
                                    <div class="flex justify-between items-center">
                                        <h5 class="text-lg font-bold">{{ $device->name }}</h5>
                                        <input type="checkbox" name="deviceIds[]" value="{{ $device->id }}"
                                               class="form-check-switch" id="device-{{ $device->id }}">
                                    </div>
                                    <p class="text-gray-500 mt-1 mb-2">{{ $device->model }}</p>
                                    <a href="javascript:pushNotificationToDevice({{ $device->id }})" class="btn btn-primary">
                                        {{ __('notification.push-to-device') }}
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p>{{ __('notification-update.no-devices') }}</p>
            @endif
        </div>
    @endif
@endsection

@push('scripts')
    <script>
        function pushMessage(notificationId) {
            fetch("{{ route('notification.push-message') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    notification_id: notificationId,
                    _action: 'pushMessage',
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.data || 'Message pushed successfully');
                    } else {
                        alert(data.message || 'Failed to push message');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to push message');
                });
        }

        function pushNotificationToDevice(deviceId) {
            fetch("{{ route('notification.push-message-to-devices') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    notification_id: {{ $notification->id }},
                    device_ids: [deviceId],
                    _action: 'pushMessageToDevices',
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        alert(data.data || 'Notification pushed successfully');
                    } else {
                        alert(data.message || 'Failed to push notification');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to push notification');
                });
        }
    </script>
@endpush