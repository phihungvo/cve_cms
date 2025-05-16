@extends ('domains.user.enterprise.eservice.tab-layout')

@section ('content')


<form method="get">
    <div class="sm:flex sm:space-x-4">
        <div class="flex-grow mt-2 sm:mt-0">
            <input type="search" class="form-control form-control-lg" placeholder="{{ __('eservice-index.filter') }}"
                data-table-search="#role-list-table" />
        </div>

        <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
            <a href="{{ route('user.enterprise.eservice.create') }}"
                class="btn form-control-lg whitespace-nowrap">{{ __('eservice-index.create') }}</a>
        </div>
    </div>
</form>

<div class="overflow-auto scroll-visible header-sticky">
    <table id="alarm-list-table"
        class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap" data-table-sort
        data-table-pagination data-table-pagination-limit="10">
        <thead>
            <tr>
                <th class="w-1">{{ __('eservice-index.enterprise_name') }}</th>
                <th class="w-1">{{ __('eservice-index.name') }}</th>
                <th class="w-1">{{ __('eservice-index.alias') }}</th>
                <th class="w-1">{{ __('eservice-index.description') }}</th>
                <th class="w-1">{{ __('eservice-index.pricing_model') }}</th>
                <th class="w-1">{{ __('eservice-index.billing_cycle') }}</th>
                <th class="w-1">{{ __('eservice-index.max_unit') }}</th>
                <th class="w-1">{{ __('eservice-index.price') }}</th>
                <th class="w-1">{{ __('eservice-index.actions') }}</th>
            </tr>
        </thead>

        <tbody>
            @foreach ($services as $row)
            @php ($link = route('user.enterprise.eservice.update', $row->id))

            <tr>
                <td class="w-1" data-table-sort-value="{{ $row->enterprise_name }}">
                    <a href="{{  $link }}" class="block">{{ $row->enterprise_name }}</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row->name }}">
                    <a href="{{ $link }}" class="block">{{ $row->name }}</a>
                </td>
                <td class="w-1" data-table-sort-value="{{ $row->alias }}">
                    <a href="{{ $link }}" class="block">{{ $row->alias }}</a>
                </td>
                <td class="w-1" data-table-sort-value="{{ $row->description }}">
                    <a href="{{ $link }}" class="block">{{ $row->description }}</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row->pricing_model }}">
                    <a href="{{ $link }}" class="block">{{ $row->pricing_model }}</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row->billing_cycle }}">
                    <a href="{{ $link }}" class="block">{{ $row->billing_cycle }}</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row->max_unit }}">
                    <a href="{{ $link }}" class="block">{{ $row->max_unit }}</a>
                </td>

                <td class="w-1" data-table-sort-value="{{ $row->price }}">
                    <a href="{{ $link }}" class="block">{{ $row->price }}</a>
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