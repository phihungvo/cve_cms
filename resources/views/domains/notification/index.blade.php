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
                <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
                    <a href="{{ route('notification.create') }}" class="btn btn-primary form-control-lg whitespace-nowrap">
                        {{ __('notification-create.title') }}
                    </a>
                </div>
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
                                @if(auth()->user()->hasRole('root') || auth()->user()->id === $item['sender_id'])
                                    <a href="{{ route('notification.update', $item['id']) }}"
                                       class="block">{{ $key + 1 }}</a>
                                @else
                                    {{ $key + 1 }}
                                @endif
                            </td>
                            <td title="{{ $item['title'] ?? '-' }}">{{ Str::limit($item['title'] ?? '-', 50, '...') }}</td>
                            <td data-toggle="tooltip" data-placement="top" title="{{ $item['content'] ?? '-' }}">
                                {{ Str::limit($item['content'] ?? '-', 50, '...') }}
                            </td>
                            <td>{{ $item['notification_type'] === 'system' ? __('System') : __('Enterprise') }}</td>
                            <td>{{ $item['target_group'] ?? __('All') }}</td>
                            <td>{{ $item['sender_name'] ?? 'N/A' }}</td>
                            <td>{{ \Carbon\Carbon::createFromTimestamp($item['created_at'])->format('Y-m-d H:i:s') }}</td>
                            <td>
                                @if($item['read_at'])
                                    <span class="text-success">{{ __('Read') }}</span>
                                @else
                                    <span class="text-danger">{{ __('Unread') }}</span>
                                @endif
                            </td>
                            <td>
                                @if(auth()->user()->hasRole('root') || auth()->user()->id === $item['sender_id'])
                                    <a href="{{ route('notification.update', $item['id']) }}"
                                       class="btn btn-primary btn-sm mr-2">{{ __('Edit') }}</a>
                                    <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                       onclick="document.getElementById('delete-notification-id').value = '{{ $item['id'] }}'; document.getElementById('delete-notification-name').innerText = '{{ addslashes($item['title']) }}';"
                                       class="btn btn-danger btn-sm">{{ __('Delete') }}</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">{{ __('No notifications found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
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
        });
    </script>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Khởi tạo tooltip của Bootstrap
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endpush