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
                                    <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                       data-action="delete" data-id="{{ $item['id'] }}" data-title="{{ addslashes($item['title'] ?? '-') }}"
                                       class="btn btn-danger form-control-lg whitespace-nowrap delete-btn">
                                        {{ __('Delete') }}
                                    </a>
                                @endif
                                @if(auth()->check() && auth()->user()->isRoot() && $item['deleted_at'])
                                    <a href="javascript:;" data-toggle="modal" data-target="#restore-modal"
                                       data-id="{{ $item['id'] }}" data-title="{{ addslashes($item['title'] ?? '-') }}"
                                       class="btn btn-success form-control-lg whitespace-nowrap restore-btn">
                                        {{ __('Restore') }}
                                    </a>
                                    <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                       data-action="force-delete" data-id="{{ $item['id'] }}" data-title="{{ addslashes($item['title'] ?? '-') }}"
                                       class="btn btn-danger form-control-lg whitespace-nowrap force-delete-btn">
                                        {{ __('Force Delete') }}
                                    </a>
                                @endif
                                @if(auth()->check() && auth()->user()->isOwner() && $item['deleted_at'] && $item['enterprise_id'] === auth()->user()->enterprise_id)
                                    <a href="javascript:;" data-toggle="modal" data-target="#restore-modal"
                                       data-id="{{ $item['id'] }}" data-title="{{ addslashes($item['title'] ?? '-') }}"
                                       class="btn btn-success form-control-lg whitespace-nowrap restore-btn">
                                        {{ __('Restore') }}
                                    </a>
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

        <!-- Restore Modal -->
        @include('molecules.restore-modal', [
            'route' => route('notification.restore', 0),
            'title' => __('notification-restore.title'),
            'message' => __('notification-restore.message', ['name' => '<span id="restore-notification-name"></span>']),
        ])

        <!-- JavaScript để xử lý modal -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Xử lý Delete và Force Delete modal
                const deleteModal = document.querySelector('#delete-modal');
                const deleteModalForm = deleteModal.querySelector('form');
                const deleteModalTitle = deleteModal.querySelector('.text-3xl');
                const deleteModalIcon = deleteModal.querySelector('[data-icon]');
                const deleteModalButton = deleteModal.querySelector('.btn:not([data-dismiss="modal"])');
                const deleteNotificationIdInput = document.createElement('input');
                deleteNotificationIdInput.type = 'hidden';
                deleteNotificationIdInput.name = 'notification_id';
                deleteNotificationIdInput.id = 'delete-notification-id';
                deleteModalForm.appendChild(deleteNotificationIdInput);

                document.querySelectorAll('.delete-btn, .force-delete-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const action = this.dataset.action;
                        const notificationId = this.dataset.id;
                        const title = this.dataset.title;

                        deleteModalTitle.textContent = action === 'force-delete' ? '{{ __('notification-delete.title') }}' : '{{ __('notification-delete.title') }}';
                        document.getElementById('delete-notification-name').textContent = title;

                        if (deleteModalIcon) {
                            deleteModalIcon.remove();
                        }
                        const iconContainer = deleteModalTitle.parentElement.querySelector('.p-5.text-center');
                        const newIcon = document.createElement('div');
                        newIcon.setAttribute('data-icon', 'true');
                        newIcon.className = 'w-16 h-16 text-theme-24 mx-auto mt-3';
                        newIcon.innerHTML = '@icon("x-circle", "w-16 h-16 text-theme-24")';
                        iconContainer.insertBefore(newIcon, deleteModalTitle);

                        deleteModalForm.action = action === 'force-delete' ? '{{ route('notification.force-delete', ':id') }}'.replace(':id', notificationId) : '{{ route('notification.delete') }}';
                        deleteNotificationIdInput.value = notificationId;

                        deleteModalForm.querySelector('input[name="_method"]').value = 'DELETE';
                        deleteModalButton.textContent = '{{ __('Delete') }}';
                        deleteModalButton.classList.remove('btn-success');
                        deleteModalButton.classList.add('btn-danger');
                    });
                });

                // Xử lý Restore modal
                const restoreModal = document.querySelector('#restore-modal');
                const restoreModalForm = restoreModal.querySelector('form');
                const restoreModalTitle = restoreModal.querySelector('.text-3xl');
                const restoreNotificationIdInput = document.createElement('input');
                restoreNotificationIdInput.type = 'hidden';
                restoreNotificationIdInput.name = 'notification_id';
                restoreNotificationIdInput.id = 'restore-notification-id';
                restoreModalForm.appendChild(restoreNotificationIdInput);

                document.querySelectorAll('.restore-btn').forEach(button => {
                    button.addEventListener('click', function () {
                        const notificationId = this.dataset.id;
                        const title = this.dataset.title;

                        restoreModalTitle.textContent = '{{ __('notification-restore.title') }}';
                        document.getElementById('restore-notification-name').textContent = title;

                        restoreModalForm.action = '{{ route('notification.restore', ':id') }}'.replace(':id', notificationId);
                        restoreNotificationIdInput.value = notificationId;
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