@extends('layouts.in')

@section('title', __('notification-show.title'))

@section('body')
    <div class="intro-y box p-5">
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4 p-4">{{ session('error') }}</div>
        @endif

        <h2 class="text-2xl font-medium mb-5">{{ __('notification-show.title') }}</h2>

        <div class="mb-4">
            <strong>{{ __('Title') }}:</strong> {{ $notification['title'] ?? '-' }}
        </div>
        <div class="mb-4">
            <strong>{{ __('Content') }}:</strong>
            <p>{{ $notification['content'] ?? '-' }}</p>
        </div>
        <div class="mb-4">
            <strong>{{ __('Type') }}:</strong>
            {{ $notification['notification_type'] === 'system' ? __('System') : __('Enterprise') }}
        </div>
        <div class="mb-4">
            <strong>{{ __('Enterprise') }}:</strong> {{ $notification['enterprise_name'] ?? 'N/A' }}
        </div>
        <div class="mb-4">
            <strong>{{ __('Target Group') }}:</strong> {{ $notification['target_group'] ?? 'All' }}
        </div>
        <div class="mb-4">
            <strong>{{ __('Sender') }}:</strong> {{ $notification['sender_name'] ?? 'N/A' }}
        </div>
        <div class="mb-4">
            <strong>{{ __('Created At') }}:</strong> {{ $notification['created_at'] }}
        </div>
        <div class="mb-4">
            <strong>{{ __('Read Status') }}:</strong>
            @if(auth()->check() && (auth()->user()->isRoot() || auth()->user()->id === $notification['sender_id']))
                @if($notification['total_count'] > 0 && $notification['read_count'] === $notification['total_count'])
                    <span class="text-success">{{ __('Read') }}</span>
                @else
                    <span class="text-danger">{{ __('Unread') }}</span>
                @endif
            @else
                @if($notification['read_at'])
                    <span class="text-success">{{ __('Read') }} ({{ $notification['read_at'] }})</span>
                @else
                    <span class="text-danger">{{ __('Unread') }}</span>
                @endif
            @endif
        </div>
        @if(auth()->check() && (auth()->user()->isRoot() || auth()->user()->id === $notification['sender_id']))
            <div class="mb-4">
                <strong>{{ __('notification-show.read-stats') }}:</strong>
                {{ $notification['read_count'] }}/{{ $notification['total_count'] }}
            </div>
            <div class="mb-4">
                <strong>{{ __('notification-show.device-stats') }}:</strong>
                <span id="device-stats">Loading...</span>
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('notification.index') }}" class="btn btn-secondary mr-2">
                {{ __('Back to List') }}
            </a>
            @if(auth()->check() && (auth()->user()->isRoot() || auth()->user()->id === $notification['sender_id']))
                <button id="push-notification" class="btn btn-primary mr-2">
                    {{ __('notification-show.push-to-devices') }}
                </button>
                <button id="resend-notification" class="btn btn-warning mr-2" disabled>
                    {{ __('notification-show.resend') }}
                </button>
            @endif
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Lấy trạng thái thiết bị
                fetch("{{ route('notification.device-status', $notification['id']) }}", {
                    method: 'GET',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! Status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.status === 'success') {
                            const { total_sent, total_read } = data.data;
                            document.getElementById('device-stats').innerText = `${total_read}/${total_sent} devices read`;

                            // Kích hoạt nút gửi lại nếu có thiết bị chưa đọc
                            if (total_read < total_sent) {
                                document.getElementById('resend-notification').disabled = false;
                            }
                        } else {
                            throw new Error(data.message || 'Failed to load device stats');
                        }
                    })
                    .catch(error => {
                        console.error('Error loading device stats:', error);
                        document.getElementById('device-stats').innerText = 'Failed to load stats: ' + error.message;
                    });

                // Xử lý sự kiện gửi thông báo
                document.getElementById('push-notification').addEventListener('click', function () {
                    fetch("{{ route('notification.push-notification') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({
                            notification_id: {{ $notification['id'] }},
                        })
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                alert('Notification pushed to ' + data.data.sent_devices.length + ' devices');
                                location.reload();
                            } else {
                                throw new Error(data.message || 'Failed to push notification');
                            }
                        })
                        .catch(error => {
                            console.error('Error pushing notification:', error);
                            alert('Failed to push notification: ' + error.message);
                        });
                });

                // Xử lý sự kiện gửi lại thông báo
                document.getElementById('resend-notification').addEventListener('click', function () {
                    fetch("{{ route('notification.resend', $notification['id']) }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                        .then(response => {
                            if (!response.ok) {
                                throw new Error(`HTTP error! Status: ${response.status}`);
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status === 'success') {
                                alert('Notification resent to ' + data.data.resent_devices.length + ' devices');
                                location.reload();
                            } else {
                                throw new Error(data.message || 'Failed to resend notification');
                            }
                        })
                        .catch(error => {
                            console.error('Error resending notification:', error);
                            alert('Failed to resend notification: ' + error.message);
                        });
                });
            });
        </script>
    @endpush
@endsection