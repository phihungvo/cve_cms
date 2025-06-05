@extends ('domains.vehicle.update-layout')
@section('content')

    <style>
        /* Ensure consistent column widths */
        .table-report th,
        .table-report td {
            width: 14.28%;
            /* Equal width for 7 columns (100% / 7) */
            padding: 8px;
            vertical-align: middle;
        }

        /* Fixed square image frame */
        .image-container {
            width: 64px;
            /* Fixed width for square frame */
            height: 64px;
            /* Fixed height for square frame */
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
            background-color: #f0f0f0;
            /* Optional: light gray background for empty images */
        }

        .image-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            /* Ensure image covers the square without distortion */
        }

        /* Ensure header and body tables align */
        .table-report {
            table-layout: fixed;
            /* Fixed layout to enforce consistent column widths */
            width: 100%;
        }
    </style>
    {{-- <input type="search" class="form-control form-control-lg mt-5"
        placeholder="{{ __('vehicle-update-image-report.filter') }}"
        data-table-search=".vehicle-update-image-report-list-table" /> --}}
    <!-- Single Table Header -->
    <div class="overflow-auto scroll-visible header-sticky">
        <table class="table table-report font-medium text-sm text-center whitespace-nowrap">
            <thead>
                <tr>
                    <th class="text-left">{{ __('vehicle-update-image-report.timestamp') }}</th>
                    <th class="text-left">{{ __('vehicle-update-image-report.type') }}</th>
                    <th class="text-left">{{ __('vehicle-update-image-report.image') }}</th>
                    <th class="text-left">{{ __('vehicle-update-image-report.latitude') }}</th>
                    <th class="text-left">{{ __('vehicle-update-image-report.longitude') }}</th>
                    <th class="text-left">{{ __('vehicle-update-image-report.device') }}</th>
                    <th class="text-left">{{ __('vehicle-update-image-report.source') }}</th>
                </tr>
            </thead>
        </table>
    </div>

    <!-- Accordion for Dates -->
    @foreach ($fpp_by_date as $date => $reports)
        @php
            // Count reports with valid minio_url (images)
            $imageCount = collect($reports)
                ->filter(function ($report) {
                    return !empty($report->minio_url);
                })
                ->count();
        @endphp
        <div x-data="{ open: {{ $loop->first ? 'true' : 'false' }} }">
            <h2 @click="open = !open" class="text-lg box p-2 mt-5 cursor-pointer">
                {{ \Carbon\Carbon::parse($date)->format('Y-m-d') }} ({{ $imageCount }}
                {{ $imageCount === 1 ? 'image' : 'images' }})
            </h2>
            <div x-show="open" @click.outside="open = false" class="overflow-auto scroll-visible">
                <table
                    class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap vehicle-update-image-report-list-table"
                    data-table-sort data-table-pagination data-table-pagination-limit="10">
                    <tbody>
                        @foreach ($reports as $each)
                            @php
                                $imageUrl = $each->minio_url
                                    ? env('MINIO_ENDPOINT') . '/' . $each->minio_bucket . '/' . $each->minio_url
                                    : null;

                                $type = 'Other';
                                if (str_contains($each->minio_url ?? '', 'fpp-start-day')) {
                                    $type = 'Start Day';
                                } elseif (str_contains($each->minio_url ?? '', 'fpp-end-day')) {
                                    $type = 'End Day';
                                }
                            @endphp
                            <tr>
                                <td class="text-left">
                                    {{ $each->created_at ? \Carbon\Carbon::parse($each->created_at)->addHours(7)->format('Y-m-d H:i:s') : '-' }}
                                </td>
                                <td class="text-left">{{ $type }}</td>
                                <td class="text-left">
                                    @if ($each->minio_url)
                                        <a href="{{ $imageUrl }}" target="_blank" class="image-container">
                                            <img src="{{ $imageUrl }}" alt="Image" />
                                        </a>
                                    @else
                                        <div class="image-container">-</div>
                                    @endif
                                </td>
                                <td class="text-left">{{ $each->latitude ?? '-' }}</td>
                                <td class="text-left">{{ $each->longitude ?? '-' }}</td>
                                <td class="text-left">{{ $each->device->name ?? '-' }}</td>
                                <td class="text-left">{{ $each->source_type ?? '-' }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endforeach
@stop
