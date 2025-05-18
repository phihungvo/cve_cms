@extends('domains.user.enterprise.eservice.tab-layout')

@section('content')

<form method="get">
    <div class="sm:flex sm:space-x-4">
        <div class="flex-grow mt-2 sm:mt-0">
            <input type="search" class="form-control form-control-lg" placeholder="{{ __('billing.index.filter') }}"
                data-table-search="#role-list-table" />
        </div>

        <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
            <a href="{{ route('user.enterprise.billing.create') }}"
                class="btn form-control-lg whitespace-nowrap">{{ __('billing.create.create') }}</a>
        </div>
    </div>
</form>

<div class="overflow-auto scroll-visible header-sticky">
    <table id="alarm-list-table"
        class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap" data-table-sort
        data-table-pagination data-table-pagination-limit="10">
        <thead>
            <tr>
                <th class="w-1">{{ __('billing.index.name') }}</th>
                <th class="w-1">{{ __('billing.index.enterprise_name') }}</th>
                <th class="w-1">{{ __('billing.index.service_name') }}</th>
                <th class="w-1">{{ __('billing.index.license_name') }}</th>
                <th class="w-1">{{ __('billing.index.usage_unit') }}</th>
                <th class="w-1">{{ __('billing.price') }}</th>
                <th class="w-1">{{ __('billing.index.payment_status') }}</th>
                <th class="w-1">{{ __('billing.start_date') }}</th>
                <th class="w-1">{{ __('billing.end_date') }}</th>
                <th class="w-1">{{ __('billing.index.actions') }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($billings as $row)
            @php ($link = route('user.enterprise.billing.update', $row['id']))

            <tr>
                <td class="w-1" data-table-sort-value="{{ $row['name'] ?? '' }}">
                    <a href="{{ $link }}" class="block">{{ $row['name'] ?? 'N/A' }}</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row['enterprise']['name'] ?? '' }}">
                    <a href="{{ $link }}" class="block">{{ $row['enterprise']['name'] ?? 'N/A' }}</a>
                </td>
                <td class="w-1" data-table-sort-value="{{ $row['service']['name'] ?? '' }}">
                    <a href="{{ $link }}" class="block">{{ $row['service']['name'] ?? 'N/A' }}</a>
                </td>
                <td class="w-1" data-table-sort-value="{{ $row['license']['name'] ?? '' }}">
                    <a href="{{ $link }}" class="block">{{ $row['license']['name'] ?? 'N/A' }}</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row['usage_unit'] ?? '' }}">
                    <a href="{{ $link }}" class="block">{{ $row['usage_unit'] ?? 'N/A' }}</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row['price'] ?? '' }}">
                    <a href="{{ $link }}" class="block">{{ $row['price'] ?? 'N/A' }}</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row['payment_status'] ?? '' }}">
                    <a href="{{ $link }}" class="block">{{ $row['payment_status'] ?? 'N/A' }}</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row['start_date'] }}">
                    <a href="{{ $link }}" class="block">@dateWithUserTimezone($row['start_date'])</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row['end_date'] }}">
                    <a href="{{ $link }}" class="block">@dateWithUserTimezone($row['end_date'])</a>
                </td>
                <td onclick="event.stopPropagation();">
                    <a href="{{ $link }}" class="btn btn-primary btn-sm">{{ __('billing.index.edit') }}</a>
                    @if ($row['deleted_at'])
                        <form action="{{ route('user.enterprise.billing.restore', $row['id']) }}" method="POST"
                            style="display:inline;" onsubmit="return confirm('{{ __('billing.index.restore_confirm') }}');">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">{{ __('billing.index.restore') }}</button>
                        </form>

                        <form action="{{ route('user.enterprise.billing.delete', $row['id']) }}" method="POST"
                            style="display:inline;"
                            onsubmit="return confirm('{{ __('billing.index.force_delete_confirm') }}');">
                            @csrf
                            <button type="submit" class="btn btn-danger btn-sm">
                                {{ __('billing.force-delete-button') }}
                            </button>
                        </form>
                    @else
                        <form action="{{ route('user.enterprise.billing.delete', $row['id']) }}" method="POST"
                            style="display:inline;" onsubmit="return confirm('{{ __('billing.index.delete_confirm') }}');">
                            @csrf
                            <button type="submit"
                                class="btn btn-danger btn-sm">{{ $row['deleted_at'] !== null ? __('billing.update.force-delete-button') : __('billing.update.soft-delete-button') }}</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@stop