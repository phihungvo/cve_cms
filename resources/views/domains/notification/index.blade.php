@php
try {
    $userId = \Illuminate\Support\Facades\Auth::id();
    $userPermission = session('userPermission_' . $userId, []);
} catch (\Exception $e) {
    $userPermission = [];
}

$allPermission = $userPermission['all'] ?? [];
@endphp

@extends('layouts.in')

@section('title', __('notification-index.title'))

@section('body')
    <div class="intro-y box p-5">
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4 p-4">{{ session('error') }}</div>
        @endif

        <!-- Search Form and Create Button -->
        <form method="get">
            <div class="sm:flex sm:space-x-4">
                <div class="flex-grow mt-2 sm:mt-0">
                    <input type="search" name="search" class="form-control form-control-lg"
                           placeholder="{{ __('notification-index.filter') }}"
                           data-table-search="#notification-list-table" value="{{ request('search') }}" />
                </div>
                @if(auth()->check() && (auth()->user()->isRoot() || auth()->user()->isOwner()))
                    <div class="sm: maglia-4 mt-2 sm:mt-0 bg-white">
                        <a href="{{ route('notification.create') }}" class="btn btn-primary form-control-lg whitespace-nowrap">
                            {{ __('notification-create.title') }}
                        </a>
                    </div>
                @endif
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-auto scroll-visible header-sticky mt-5">
            <table id="notification-list-table" class="table table-report sm:mt-2 font-medium text-center whitespace-nowrap"
                   data-table-sort data-table-pagination data-table-pagination-limit="10">
                <thead>
                    <tr>
                        <th class="w-1">{{ __('STT') }}</th>
                        <th>{{ __('Title') }}</th>
                        <th>{{ __('Content') }}</th>
                        <th>{{ __('Type') }}</th>
                        <th>{{ __('Enterprise') }}</th>
                        <th>{{ __('Target Group') }}</th>
                        <th>{{ __('Sender') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Read Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($notifications as $key => $item)
                        <tr>
                            <td class="w-1">
                                @if(auth()->check() && (auth()->user()->isRoot() || (auth()->user()->isOwner() && $item['enterprise_id'] === auth()->user()->enterprise_id)))
                                    <a href="{{ route('notification.update', $item['id']) }}"
                                       class="block">{{ $key + 1 }}</a>
                                @else
                                    {{ $key + 1 }}
                                @endif
                            </td>
                            <td title="{{ $item['title'] ?? '-' }}">{{ \Illuminate\Support\Str::limit($item['title'] ?? '-', 50, '...') }}</td>
                            <td data-toggle="tooltip" data-placement="top" title="{{ $item['content'] ?? '-' }}">
                                {{ \Illuminate\Support\Str::limit($item['content'] ?? '-', 50, '...') }}</td>
                            </td>
                            <td>{{ $item['notification_type'] === 'system' ? __('System') : __('Enterprise') }}</td>
                            <td>{{ $item['enterprise_id'] ? \App\Domains\User\Enterprise\Model\Enterprise::find($item['enterprise_id'])?->name : 'N/A' }}</td>
                            <td>{{ $item['target_group'] ?? 'All' }}</td>
                            <td>{{ $item['sender_name'] ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::createFromTimestamp($item['created_at'])->format('Y-m-d H:i:s') }}</td>
                            <td>
                                @if($item['read_at'] || ($item['total_count'] > 0 && $item['read_count'] === $item['total_count']))
                                    <span class="text-success">{{ __('Read') }}</span>
                                @else
                                    <span class="text-danger">{{ __('Unread') }}</span>
                                @endif
                                @if(auth()->check() && (auth()->user()->isRoot() || auth()->user()->id === $item['sender_id']))
                                    <br>
                                    <span>{{ $item['read_count'] }}/{{ $item['total_count'] }} {{ __('notification-index.read-stats') }}</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('notification.show', $item['id']) }}" class="btn btn-info form-control-lg whitespace-nowrap">
                                    {{ __('View Details') }}
                                </a>
                                @if(auth()->check() && (auth()->user()->isRoot() || (auth()->user()->isOwner() && $item['enterprise_id'] === auth()->user()->enterprise_id)))
                                    <a href="{{ route('notification.update', $item['id']) }}" class="btn btn-primary form-control-lg whitespace-nowrap">
                                        {{ __('notification-update.title') }}
                                    </a>
                                @endif
                                @if(auth()->check() && !$item['read_at'] && !auth()->user()->isRoot())
                                    <form action="{{ route('notification.read', $item['id']) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-success form-control-lg whitespace-nowrap mark-as-read" data-id="{{ $item['id'] }}">
                                            {{ __('Mark as Read') }}
                                        </button>
                                    </form>
                                @endif
                                @if(auth()->check() && !$item['deleted_at'] && (auth()->user()->isRoot() || (auth()->user()->isOwner() && $item['enterprise_id'] === auth()->user()->enterprise_id)))
                                    <form action="{{ route('notification.delete') }}" method="POST" style="display:inline;" class="delete-form" data-id="{{ $item['id'] }}">
                                        @csrf
                                        @method('DELETE')
                                        <input type="hidden" name="notification_id" value="{{ $item['id'] }}">
                                        <button type="submit" class="btn btn-danger form-control-lg whitespace-nowrap delete-btn">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                @endif
                                @if(auth()->check() && auth()->user()->isRoot() && $item['deleted_at'])
                                    <form action="{{ route('notification.restore', $item['id']) }}" method="POST" style="display:inline;" class="restore-form" data-id="{{ $item['id'] }}">
                                        @csrf
                                        <button type="submit" class="btn btn-warning form-control-lg whitespace-nowrap restore-btn">
                                            {{ __('Restore') }}
                                        </button>
                                    </form>
                                    <form action="{{ route('notification.force-delete', $item['id']) }}" method="POST" style="display:inline;" class="force-delete-form" data-id="{{ $item['id'] }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-dark form-control-lg whitespace-nowrap force-delete-btn">
                                            {{ __('Force Delete') }}
                                        </button>
                                    </form>
                                @endif
                                @if(auth()->check() && auth()->user()->isOwner() && $item['deleted_at'] && $item['enterprise_id'] === auth()->user()->enterprise_id)
                                    <form action="{{ route('notification.restore', $item['id']) }}" method="POST" style="display:inline;" class="restore-form" data-id="{{ $item['id'] }}">
                                        @csrf
                                        <button type="submit" class="btn btn-warning form-control-lg whitespace-nowrap restore-btn">
                                            {{ __('Restore') }}
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">{{ __('No notifications found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Delete Modal -->
        @include('molecules.delete-modal', [
            'method' => 'delete',
            'route' => route('notification.delete'),
            'title' => __('notification-delete.title'),
            'message' => __('notification-delete.message', ['name' => '<span id="delete-notification-name"></span>']),
        ])

        <!-- Form ẩn để lưu notification ID -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const form = document.querySelector('#delete-modal form');
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'notification_id';
                input.id = 'delete-notification-id';
                form.appendChild(input);

                // Xử lý xác nhận trước khi xóa
                document.querySelectorAll('.delete-form, .force-delete-form, .restore-form').forEach(form => {
                    form.addEventListener('submit', function (e) {
                        if (!confirm('Are you sure you want to perform this action?')) {
                            e.preventDefault();
                        }
                    });
                });
            });
        </script>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.mark-as-read').forEach(button => {
                button.addEventListener('click', function (e) {
                    e.preventDefault();
                    const notificationId = this.dataset.id;
                    fetch(`/notification/${notificationId}/read`, {
                        method: 'PATCH',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Content-Type': 'application/json',
                        },
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            alert(data.message);
                            location.reload();
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => {
                        alert('Error: ' + error.message);
                    });
                });
            });
        });
    </script>
@endpush