@extends('layouts.in')

@section('body')
    <!-- Search Form -->
    <form method="GET" class="sm:flex sm:space-x-4">
        <div class="flex-grow mt-2 sm:mt-0">
            <input type="search" name="search" class="form-control form-control-lg"
                   placeholder="{{ __('role-index.Search...') }}" value="{{ old('search', $search) }}"
                   data-table-search="#role-list-table">
        </div>
        <div class="sm:ml-4 mt-2 sm:mt-0">
            <button type="submit" class="btn form-control-lg bg-white">
                {{ __('role-index.Search') }}
            </button>
        </div>
    </form>

    <!-- Data Table -->
    <div class="overflow-auto scroll-visible header-sticky mt-4">
        <table id="role-list-table"
               class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap"
               data-table-sort
               data-table-pagination data-table-pagination-limit="10">
            <thead>
            <tr>
                <th class="w-1">{{ __('role-index.No') }}</th>
                <th class="text-left w-1">{{ __('role-index.Name') }}</th>
                <th class="text-left w-1">{{ __('role-index.Enterprise') }}</th>
                <th class="w-1">{{ __('role-index.Created At') }}</th>
                <th class="w-1">{{ __('role-index.Actions') }}</th>
            </tr>
            </thead>

            <tbody>
            @forelse($roles as $index => $role)
                <tr>
                    <td>{{ $index + 1 }}</td> <!-- Số thứ tự đơn giản cho Collection -->
                    <td class="text-left">{{ $role['name'] }}</td>
                    <td class="text-left">{{ $role['enterprise_name'] ?? 'system_owner' }}</td>
                    <td data-table-sort-value="{{ $role['created_at'] }}">
                        {{ \Carbon\Carbon::parse($role['created_at'])->format('d/m/Y H:i') }}
                    </td>
                    <td>
                        @if($role['deleted_at'] == null)
                            <!-- Btn Edit -->
                            <a class="btn btn-primary min-w-5"
                               href="{{ route('user.role.edit', $role['id']) }}">
                                {{ __('role-index.Edit') }}
                            </a>
                            <!-- Btn Delete -->
                            <a href="{{ route('user.role.destroy', ['id' => $role['id']]) }}"
                               class="btn btn-outline-danger min-w-5"
                               onclick="event.preventDefault(); document.getElementById('inactive-form-{{ $role['id'] }}').submit();">
                                {{ __('role-index.Delete') }}
                            </a>
                            <form id="inactive-form-{{ $role['id'] }}"
                                  action="{{ route('user.role.destroy', $role['id']) }}" method="POST"
                                  style="display:none;">
                                @csrf
                                @method('DELETE')
                            </form>
                        @else
                            <!-- Btn Restore -->
                            <a class="btn btn-success min-w-5"
                               href="{{ route('user.role.restore', ['id' => $role['id']]) }}"
                               onclick="event.preventDefault(); document.getElementById('restore-form-{{ $role['id'] }}').submit();">
                                {{ __('role-index.restore-button') }}
                            </a>
                            <form id="restore-form-{{ $role['id'] }}"
                                  action="{{ route('user.role.restore', ['id' => $role['id']]) }}" method="POST"
                                  style="display:none;">
                                @csrf
                                @method('PATCH')
                            </form>
                            <!-- Btn Force Delete -->
                            <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                               class="btn btn-danger min-w-5">
                                {{ __('role-index.fore-delete-button') }}
                            </a>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center"> <!-- Giảm colspan từ 6 xuống 5 vì không có cột phân trang -->
                        {{ __('role-index.No data available') }}
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <!-- Modal Force Delete cho từng role -->
    @if(!$roles->isEmpty())
        @include('molecules.delete-modal',[
        'route' => route('user.role.force-delete', ['id' => $role['id']]),
        'title' => __('role-index.delete-title'),
        'message' => __('role-index.delete-message'),
        'method' => 'delete'
        ])
    @endif
@endsection
