@extends('layouts.in')

@section('body')
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <form method="GET" action="{{ route('campaign.index') }}">
        <div class="sm:flex sm:space-x-4">
            <div class="flex-grow mt-2 sm:mt-0">
                <input type="search" name="search" class="form-control form-control-lg"
                    placeholder="{{ __('campaign-index.search') }}" value="{{ $search }}"
                    data-table-search="#campaign-list-table" />
            </div>
            @php
                $isRoot = auth()->check() && auth()->user()->isRoleRoot();
            @endphp
            @if ($isRoot)
                <div class="flex-grow mt-2 lg:mt-0">
                    <x-select name="enterprise_id" :options="$enterprises" value="id" text="name"
                        placeholder="{{ __('campaign-index.all_enterprises') }}" data-change-submit></x-select>
                </div>
            @endif
            <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
                <a href="{{ route('campaign.create') }}" class="btn form-control-lg whitespace-nowrap">
                    {{ __('campaign-index.create') }}
                </a>
            </div>
        </div>
    </form>

    <div class="overflow-auto scroll-visible header-sticky">
        <table id="campaign-list-table"
            class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap" data-table-sort>
            <thead>
                <tr>
                    <th>{{ __('campaign-index.stt') }}</th>
                    <th>{{ __('campaign-index.name') }}</th>
                    @if ($isRoot)
                        <th>{{ __('campaign-index.enterprise') }}</th>
                    @endif
                    <th>{{ __('campaign-index.users') }}</th>
                    <th>{{ __('campaign-index.media') }}</th>
                    <th>{{ __('campaign-index.budget') }}</th>
                    <th>{{ __('campaign-index.reach') }} (Actual/Target)</th>
                    <th>{{ __('campaign-index.impression') }} (Actual/Target)</th>
                    <th>{{ __('campaign-index.distance') }} (Actual/Target)</th>
                    <th>{{ __('campaign-index.no_device') }}</th> <!-- Added no_device column -->
                    <th>{{ __('campaign-index.cpm') }} (Actual/Target)</th>
                    <th>{{ __('campaign-index.city') }}</th>
                    <th>{{ __('campaign-index.status') }}</th>
                    <th>{{ __('campaign-index.actions') }}</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($campaign as $key => $item)
                    <tr onclick="window.location='{{ route('campaign.edit', $item['id']) }}'" style="cursor: pointer;">
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $item['name'] }}</td>
                        @if ($isRoot)
                            <td>{{ $item['enterprise_name'] }}</td>
                        @endif
                        <td>{{ $item['user_names'] ?? 'N/A' }}</td>
                        <td>
                            @php
                                $mediaCount = !empty($item['media_names'])
                                    ? count(array_filter(explode(',', $item['media_names'])))
                                    : 0;
                            @endphp
                            {{ $mediaCount }}
                        </td>
                        <td>{{ number_format($item['budget'], 2) }}</td>
                        <td>{{ number_format($item['reach']['actual'], 0) }} /
                            {{ number_format($item['reach']['target'], 0) }}</td>
                        <td>{{ number_format($item['impression']['actual'], 0) }} /
                            {{ number_format($item['impression']['target'], 0) }}</td>
                        <td>{{ number_format($item['distance']['actual'], 0) }} /
                            {{ number_format($item['distance']['target'], 0) }}</td>
                        <td>{{ number_format($item['no_device'], 0) }}</td> <!-- Display no_device -->
                        <td>{{ number_format($item['cpm']['actual'], 2) }} /
                            {{ number_format($item['cpm']['target'], 2) }}</td>
                        <td>{{ $item['city'] }}</td>
                        <td>
                            @php
                                $currentDate = now();
                                $startTime = \Carbon\Carbon::parse($item['start_time']);
                                $endTime = \Carbon\Carbon::parse($item['end_time']);
                                if ($currentDate->between($startTime, $endTime)) {
                                    $status = __('campaign-index.running');
                                } elseif ($currentDate->lessThan($startTime)) {
                                    $status = __('campaign-index.not_started');
                                } else {
                                    $status = __('campaign-index.completed');
                                }
                            @endphp
                            {{ $status }}
                        </td>
                        <td onclick="event.stopPropagation();">
                            <a href="{{ route('campaign.edit', $item['id']) }}"
                                class="btn btn-primary btn-sm">{{ __('campaign-index.edit') }}</a>
                            @if ($item['deleted_at'])
                                <form action="{{ route('campaign.restore', $item['id']) }}" method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('{{ __('campaign-index.restore_confirm') }}');">
                                    @csrf
                                    <button type="submit"
                                        class="btn btn-success btn-sm">{{ __('campaign-index.restore') }}</button>
                                </form>
                                <form action="{{ route('campaign.forceDelete', $item['id']) }}" method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('{{ __('campaign-index.force_delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-danger btn-sm">{{ __('campaign-index.force_delete') }}</button>
                                </form>
                            @else
                                <form action="{{ route('campaign.destroy', $item['id']) }}" method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm('{{ __('campaign-index.delete_confirm') }}');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="btn btn-danger btn-sm">{{ __('campaign-index.delete') }}</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="{{ $isRoot ? 13 : 12 }}">{{ __('campaign-index.no_data') }}</td>
                        <!-- Adjusted colspan -->
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <style>
        #campaign-list-table tbody tr:hover {
            background-color: #ffffff;
        }

        #campaign-list-table td:nth-child({{ $isRoot ? 12 : 11 }}) {
            font-weight: bold;
        }
    </style>
@stop
