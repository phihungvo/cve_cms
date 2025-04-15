@extends('layouts.in')

@section('title', __('schedule-detail-index.title'))

@section('body')
    <div class="intro-y box p-5">
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger mb-4 p-4">
                {{ session('error') }}
            </div>
        @endif

        <!-- Search Form and Create Button -->
        <form method="get">
            <div class="sm:flex sm:space-x-4">
                <div class="flex-grow mt-2 sm:mt-0">
                    <input type="search" name="search" class="form-control form-control-lg"
                        placeholder="{{ __('schedule-detail-index.filter') }}" data-table-search="#scheduledetail-detail-list-table"
                        value="{{ request('search') }}" />
                </div>

                <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
                    <a href="{{ route('schedule-detail.create') }}" class="btn form-control-lg whitespace-nowrap">
                        {{ __('schedule-detail-index.create') }}
                    </a>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-auto scroll-visible header-sticky mt-5">
            <table id="schedule-detail-list-table"
                class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap" data-table-sort
                data-table-pagination data-table-pagination-limit="10">
                <thead>
                    <tr>
                        <th class="w-1">{{ __('ID') }}</th>
                        <th>{{ __('Schedule Name') }}</th>
                        <th>{{ __('Schedule Description') }}</th>
                        <th>{{ __('Playlist Name') }}</th>
                        <th>{{ __('Start Time') }}</th>
                        <th>{{ __('End Time') }}</th>
                        <th>{{ __('Repeat') }}</th>
                        <th>{{ __('Active') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schedules as $key => $schedule)
                        <tr>
                            <td class="w-1">{{ $key + 1 }}</td> 
                            <td><a href="{{ route('schedule-detail.update', $schedule->id) }}"
                                    class="block">{{ $schedule->schedule ? $schedule->schedule->name : '-' }}</a></td>
                            <td><a href="{{ route('schedule-detail.update', $schedule->id) }}"
                                    class="block">{{ $schedule->schedule ? $schedule->schedule->description : '-' }}</a></td>
                            <td><a href="{{ route('schedule-detail.update', $schedule->id) }}"
                                    class="block">{{ $schedule->playlist ? $schedule->playlist->name : '-' }}</a></td>
                            <td>{{ $schedule->start_time ? $schedule->start_time->format('Y-m-d H:i:s') : '-' }}</td>
                            <td>{{ $schedule->end_time ? $schedule->end_time->format('Y-m-d H:i:s') : '-' }}</td>
                            <td>
                                <form action="{{ route('schedule-detail.toggle', $schedule->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="field" value="repeat">
                                    <input type="checkbox" name="repeat" value="1"
                                        onchange="this.form.submit()"
                                        {{ $schedule->repeat ? 'checked' : '' }}
                                        style="border: 2px solid #34a853; accent-color: #34a853; width: 15px; height: 15px;">
                                </form>
                            </td>
                            <td>
                                <form action="{{ route('schedule-detail.toggle', $schedule->id) }}" method="POST" class="inline">
                                    @csrf
                                    @method('PATCH')
                                    <input type="hidden" name="field" value="active">
                                    <input type="checkbox" name="active" value="1"
                                        onchange="this.form.submit()"
                                        {{ $schedule->active ? 'checked' : '' }}
                                        style="border: 2px solid #34a853; accent-color: #34a853; width: 15px; height: 15px;">
                                </form>
                            </td>
                            <td>{{ $schedule->created_at->format('Y-m-d H:i:s') }}</td>
                            <td>
                                <!-- Nút Edit -->
                                <a href="{{ route('schedule-detail.update', $schedule->id) }}" class="btn btn-primary btn-sm mr-1">{{ __('Edit') }}</a>

                                <!-- Nút Delete với Modal -->
                                <a href="javascript:;" data-toggle="modal" 
                                   data-target="#delete-modal"
                                   onclick="document.getElementById('delete-schedule-id').value = '{{ $schedule->id }}'; document.getElementById('delete-schedule-name').innerText = '{{ $schedule->schedule ? $schedule->schedule->name : ($schedule->playlist ? $schedule->playlist->name : '-') }}';"
                                   class="btn btn-danger btn-sm">{{ __('Delete') }}</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">{{ __('No schedules found') }}</td> <!-- Sửa colspan từ 11 thành 10 vì đã bỏ cột Playlist Description -->
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include modal duy nhất -->
    @include('molecules.delete-modal', [
        'method' => 'delete',
        'route' => route('schedule-detail.delete', 0),
        'title' => __('schedule-detail-delete.title'),
        'message' => '<span id="delete-schedule-name"></span>',
    ])

    <!-- Form ẩn để lưu schedule ID -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('#delete-modal form');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'schedule_id';
            input.id = 'delete-schedule-id';
            form.appendChild(input);
        });
    </script>
@endsection