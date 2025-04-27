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

@section('body')

<form method="get">
    @php use Illuminate\Support\Facades\Log; @endphp

    <div class="sm:flex sm:space-x-4">
        <div class="flex-grow mt-2 sm:mt-0">
            <input type="search" class="form-control form-control-lg" placeholder="{{ __('device-index.filter') }}"
                data-table-search="#device-list-table" />
        </div>

        @if(isset($allPermission[App\Domains\User\Role\Enum\RoleEnum::ROOT->value]) || isset($allPermission[App\Domains\User\Role\Enum\RoleEnum::OWNER->value]))
            @if ($users_multiple)
                <div class="flex-grow mt-2 lg:mt-0">
                    <x-select name="user_id" :options="$users" class="cursor-pointer" value="id" text="name"
                              placeholder="{{ __('device-index.select_user') }}" data-change-submit>
                    </x-select>
                </div>
            @endif
        @endif

        <div class="flex-grow mt-2 lg:mt-0">
            <x-select name="vehicle_id" :options="$vehicles" value="id" text="name" class="cursor-pointer"
                placeholder="{{ __('device-index.select_vehicle') }}" data-change-submit></x-select>
        </div>

        @if(isset($allPermission[App\Domains\User\Role\Enum\RoleEnum::ROOT->value]))
            <div class="flex-grow mt-2 lg:mt-0">
                <x-select name="device_type" :options="$device_type" value="name" text="name" class="cursor-pointer"
                    placeholder="{{ __('device-index.select_device_type') }}" data-change-submit></x-select>
            </div>
        @endif

        <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
            <a href="{{ route('device.map') }}"
                class="btn form-control-lg whitespace-nowrap">{{ __('device-index.map') }}</a>
        </div>

        <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
            <a href="{{ route('device.create') }}"
                class="btn form-control-lg whitespace-nowrap">{{ __('device-index.create') }}</a>
        </div>
    </div>
</form>

<div class="overflow-auto scroll-visible header-sticky">
    <table id="device-list-table"
        class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap" data-table-sort
        data-table-pagination data-table-pagination-limit="10">
        <thead>
            <tr>
                <th>{{ __('device-index.name') }}</th>
                <th>{{ __('device-index.model') }}</th>
                @if ($user_empty)
                    <th>{{ __('device-index.user') }}</th>
                @endif
                @if (auth()->user()->isRoot()) <!-- Hiển thị cột enterprise cho root -->
                    <th>{{ __('device-index.enterprise') }}</th>
                @endif
                <th>{{ __('device-index.device_type') }}</th>
                @if ($vehicle_empty)
                    <th>{{ __('device-index.vehicle') }}</th>
                @endif
                <th>{{ __('device-index.updated_at') }}</th>
                <th>{{ __('device-index.shared') }}</th>
                <th>{{ __('device-index.enabled') }}</th>
                <th>{{ __('device-index.shared_public') }}</th>
                <th>{{ __('trip-index.messages') }}</th>
                <th>Status</th>

            </tr>
        </thead>

        <tbody>
            @foreach ($list as $row)
                        @php
    $link = route('device.update', $row->id);
    $updatedAt = isset($row->deviceStatus['updated_at'])
        ? \Carbon\Carbon::parse($row->deviceStatus['updated_at'])->setTimezone('Asia/Ho_Chi_Minh')
        : null;

    $isOnline = false; // Mặc định là Offline

    if ($updatedAt) {
        $currentDate = now()->setTimezone('Asia/Ho_Chi_Minh');
        $updatedDate = $updatedAt;

        // Ghi log để kiểm tra


        // Kiểm tra xem có cùng ngày không và updated_at mới hơn currentDate
        if ($currentDate->isSameDay($updatedDate) && $updatedDate->greaterThanOrEqualTo($currentDate->subMinutes(15))) {
            $isOnline = true;
        } else {
            $isOnline = false;
        }
    } else {
        Log::warning('Không tìm thấy updated_at cho thiết bị: ' . $row->name);
    }
                        @endphp

                        <tr>
                            <td><a href="{{ $link }}" class="block">{{ $row->name }}</a></td>
                            <td><a href="{{ $link }}" class="block">{{ $row->model }}</a></td>
                            @if ($user_empty)
                                <td><a href="{{ $link }}" class="block">{{ $row->user->name?? '-' }}</a></td>
                            @endif
                            @if (auth()->user()->isRoot())
                                <td><a href="{{ $link }}" class="block">{{ $row->enterprise->name ?? '-' }}</a></td>
                            @endif
                            <td><a href="{{ $link }}" class="block">{{ $row->deviceType->name ?? '-' }}</a></td>
                            @if ($vehicle_empty)
                                <td><a href="{{ $link }}" class="block">{{ $row->vehicle->name ?? '-' }}</a></td>
                            @endif
                            <td>{{ $updatedAt ? $updatedAt->format('Y-m-d H:i:s') : '-' }}</td>
                            <td data-table-sort-value="{{ (int) $row->enabled }}" class="w-1">@status($row->enabled)</td>
                            <td data-table-sort-value="{{ (int) $row->shared }}" class="w-1">
                                <a href="{{ route('device.update.boolean', [$row->id, 'shared']) }}" class="block"
                                    data-update-boolean="shared">@status($row->shared)</a>
                            </td>
                            <td data-table-sort-value="{{ (int) $row->shared_public }}" class="w-1">
                                <a href="{{ route('device.update.boolean', [$row->id, 'shared_public']) }}" class="block"
                                    data-update-boolean="shared_public">@status($row->shared_public)</a>
                            </td>
                            <td class="w-1">
                                <a href="{{ route('device.update.device-message', $row->id) }}"
                                    class="{{ $row->messages_pending_count ? 'text-warning' : 'text-success' }}">
                                    {{ $row->messages_count . ($row->messages_pending_count ? ('/' . $row->messages_pending_count) : '') }}
                                </a>
                            </td>
                            <td class="w-1">
                                <span class="{{ $isOnline ? 'text-success' : 'text-danger' }}">
                                    {{ $isOnline ? 'Online' : 'Offline' }}
                                </span>
                            </td>

                        </tr>

            @endforeach
        </tbody>
    </table>
</div>

@stop
