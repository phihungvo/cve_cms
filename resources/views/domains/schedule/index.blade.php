@php
    try {
        $userId = \Illuminate\Support\Facades\Auth::id(); // Lấy ID của user hiện tại
        $userPermission = session('userPermission_' . $userId, []); // Lấy từ session, mặc định là mảng rỗng nếu không có
    } catch (\Exception $e) {
        $userPermission = [];
    }

    $allPermission = $userPermission['all'] ?? [];
@endphp

@extends ('domains.schedule.index-layout')

@extends('layouts.in')

@section('title', __('schedule-index.title'))

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
                           placeholder="{{ __('schedule-index.filter') }}"
                           data-table-search="#scheduledetail-detail-list-table" value="{{ request('search') }}"/>
                </div>
                @if(auth()->user()->isRoleRoot())
                    <div class="sm:ml-4 mt-2 sm:mt-0">
                        <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                                  placeholder="{{__('schedule-index.select-enterprise')}}" data-change-submit></x-select>
                    </div>
                @endif
                <div class="sm:ml-4 mt-2 sm:mt-0">
                    <x-select name="schedule_group_id" :options="$scheduleGroups" value="id" text="name"
                              placeholder="{{__('schedule-index.select-schedule-group')}}" data-change-submit></x-select>
                </div>

                <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
                    <a href="{{ route('schedule.create') }}" class="btn form-control-lg whitespace-nowrap">
                        {{ __('schedule-index.create') }}
                    </a>
                </div>
            </div>
        </form>

        <!-- Table -->
        <div class="overflow-auto scroll-visible header-sticky mt-5">
            <table id="schedule-list-table"
                   class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap"
                   data-table-sort
                   data-table-pagination data-table-pagination-limit="10">
                <thead>
                <tr>
                    <th class="w-1">{{ __('ID') }}</th>
                    @if(isset($allPermission[App\Domains\User\Role\Enum\RoleEnum::ROOT->value]))
                        <th>{{ __('Enterprise') }}</th>
                    @endif
                    <th>{{ __('Schedule Name') }}</th>
                    <th>{{ __('Schedule Description') }}</th>
                    <th>{{ __('Playlist Name') }}</th>
                    <th>{{__('Displays')}}</th>
                    <th>{{ __('Start Time') }}</th>
                    <th>{{ __('End Time') }}</th>
                    <th>{{ __('Actions') }}</th>
                </tr>
                </thead>
                <tbody>
                @forelse($schedules as $key => $schedule)
                    <tr>
                        {{-- ID --}}
                        <td class="w-1">{{ $key + 1 }}</td>

                        @if(isset($allPermission[App\Domains\User\Role\Enum\RoleEnum::ROOT->value]))
                            <td><a href="{{ route('schedule.update', $schedule->id) }}"
                                   class="block">{{ $schedule->enterprise_name ?? '-' }}</a></td>
                        @endif

                        <td><a href="{{ route('schedule.update', $schedule->id) }}"
                               class="block">{{ $schedule->name ?? '-' }}</a></td>
                        <td class="max-w-36 whitespace-nowrap overflow-hidden text-ellipsis" title="{{$schedule->description ?? ''}}">
                           <a href="{{route('schedule.update', $schedule->id)}}">
                               {{ $schedule->description ?? '-' }}
                            </a>
                        </td>
                        <td><a href="{{ route('schedule.update', $schedule->id) }}"
                               class="block">{{ $schedule->playlist->name ?? '-' }}</a>
                        </td>
                        <td>{{$schedule->published_display_count}}</td>
                        <td>{{ $schedule->start_time?->format('Y-m-d H:i:s') ?? '-' }}</td>
                        <td>{{ $schedule->end_time?->format('Y-m-d H:i:s') ?? '-' }}</td>

                        <td>
                            @php
                                $isActive = is_null($schedule->deleted_at);
                                $displayName = $schedule->schedule->name ?? $schedule->playlist->name ?? '-';
                            @endphp

                                <!-- Edit Button -->
                            <a href="{{ route('schedule.update', $schedule->id) }}"
                               class="btn btn-primary btn-sm mr-1">{{ __('Edit') }}</a>

                            @if($isActive)
                                <!-- Delete Button with Modal -->
                                <a href="javascript:;" data-toggle="modal" data-target="#delete-modal" onclick="document.getElementById('delete-schedule-id').value = '{{ $schedule->id }}';
                                                                                                                                                    document.getElementById('delete-schedule-name').innerText = '{{ $displayName }}';"
                                   class="btn btn-danger btn-sm">{{ __('Delete') }}</a>
                            @else
                                <!-- Restore Button -->
                                <a href="javascript:;" data-toggle="modal" data-target="#restore-modal"
                                   class="btn btn-success btn-sm mr-2">{{ __('schedule-update.restore-button') }}</a>
                                @include('molecules.restore-modal', [
                                    'title' => __('schedule-update.restore-title'),
                                    'message' => __('schedule-update.restore-message'),
                                    'action' => 'restore',
                                    'route' => route('schedule.restore', $schedule->id),
                                    'method' => 'PATCH',
                                ])

                                <!-- Force Delete Button -->
                                <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                   class="btn btn-danger btn-sm mr-2">{{ __('schedule-delete.force-delete-button') }}</a>

                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center">{{ __('No schedules found') }}</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Include modal duy nhất -->
    @include('molecules.delete-modal', [
'method' => 'delete',
'route' => route('schedule.delete', ['schedule_id' => $schedule->id ?? '']),
'title' => __('schedule-delete.title'),
'message' => '<span id="delete-schedule-name"></span>',
])


    <!-- Form ẩn để lưu schedule ID -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.querySelector('#delete-modal form');
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'schedule_id';
            input.id = 'delete-schedule-id';
            form.appendChild(input);
        });
    </script>
@endsection
