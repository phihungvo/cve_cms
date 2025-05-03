@extends ('domains.user.enterprise.eservice.tab-layout')

@section ('content')


<form method="get">
    <div class="sm:flex sm:space-x-4">
        <div class="flex-grow mt-2 sm:mt-0">
            <input type="search" class="form-control form-control-lg" placeholder="{{ __('license-index.filter') }}"
                data-table-search="#role-list-table" />
        </div>

        <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
            <a href="{{ route('user.enterprise.eservice.create') }}"
                class="btn form-control-lg whitespace-nowrap">{{ __('license-index.create') }}</a>
        </div>
    </div>
</form>

<div class="overflow-auto scroll-visible header-sticky">
    <table id="alarm-list-table"
        class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap" data-table-sort
        data-table-pagination data-table-pagination-limit="10">
        <thead>
            <tr>
                <th class="w-1">{{ __('license-index.enterprise_name') }}</th>
                <th class="w-1">{{ __('license-index.service_name') }}</th>
                <th class="w-1">{{ __('license-index.type') }}</th>
                <th class="w-1">{{ __('license-index.status') }}</th>
                <th class="w-1">{{ __('license-index.max_user') }}</th>
                <th class="w-1">{{ __('license-index.max_device') }}</th>
                <th class="w-1">{{ __('license-index.start_date') }}</th>
                <th class="w-1">{{ __('license-index.end_date') }}</th>
                <th class="w-1">{{ __('license-index.actions') }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($licenses as $row)
            @php ($link = route('user.enterprise.eservice.update', $row->id))

            <tr>
                <td class="w-1" data-table-sort-value="{{ $row->enterprise_name }}">
                    <a href="{{ $link }}" class="block">{{ $row->enterprise_name }}</a>
                </td>
                <td class="w-1" data-table-sort-value="{{ $row->service_name }}">
                    <a href="{{ $link }}" class="block">{{ $row->service_name }}</a>
                </td>
                <td class="w-1" data-table-sort-value="{{ $row->license_type }}">
                    <a href="{{ $link }}" class="block">{{ $row->license_type }}</a>
                </td>
                <td class="w-1" data-table-sort-value="{{ $row->status }}">
                    <a href="{{ $link }}" class="block">{{ $row->status }}</a>

                <td class="w-1" data-table-sort-value="{{ $row->max_users }}">
                    <a href="{{ $link }}" class="block">{{ $row->max_users }}</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row->max_devices }}">
                    <a href="{{ $link }}" class="block">{{ $row->max_devices }}</a>
                </td>


                <td class="w-1" data-table-sort-value="{{ $row->start_date }}">
                    <a href="{{ $link }}" class="block">@dateWithUserTimezone($row->start_date)</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row->end_date }}">
                    <a href="{{ $link }}" class="block">@dateWithUserTimezone($row->end_date)</a>
                </td>
                <td onclick="event.stopPropagation();">
                    <a href="{{ $link }}" class="btn btn-primary btn-sm">{{ __('eservice-index.edit') }}</a>
                    @if ($row['deleted_at'])
                        <form action="{{ route('user.enterprise.eservice.restore', $row['id']) }}" method="POST"
                            style="display:inline;"
                            onsubmit="return confirm('{{ __('eservice-index.restore_confirm') }}');">
                            @csrf
                            <button type="submit" class="btn btn-success btn-sm">{{ __('eservice-index.restore') }}</button>
                        </form>

                        <form action="{{ route('user.enterprise.eservice.delete', $row['id']) }}" method="POST"
                            style="display:inline;"
                            onsubmit="return confirm('{{ __('eservice-index.force_delete_confirm') }}');">
                            <button type="submit" class="btn btn-danger btn-sm">
                                {{  __('eservice-update.force-delete-button') }}
                            </button>
                        </form>
                    @else
                        <form action="{{ route('user.enterprise.eservice.delete', $row['id']) }}" method="POST"
                            style="display:inline;" onsubmit="return confirm('{{ __('eservice-index.delete_confirm') }}');">

                            <button type="submit"
                                class="btn btn-danger btn-sm">{{ $row->deleted_at !== null ? __('eservice-update.force-delete-button') : __('eservice-update.soft-delete-button') }}</button>
                        </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

@stop