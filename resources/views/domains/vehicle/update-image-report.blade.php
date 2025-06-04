@extends ('domains.vehicle.update-layout')

@section('content')

    <input type="search" class="form-control form-control-lg mt-5"
        placeholder="{{ __('vehicle-update-image-report.filter') }}"
        data-table-search="#vehicle-update-image-report-list-table" />

    <div class="overflow-auto scroll-visible header-sticky">
        <table id="vehicle-update-image-report-list-table"
            class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap" data-table-sort
            data-table-pagination data-table-pagination-limit="10">

            <thead>
                <tr>
                    <th class="text-left">{{ __('vehicle-update-image-report.image') }}</th>
                    <th class="text-left">{{ __('vehicle-update-image-report.timestamp') }}</th>
                    <th class="text-left">{{ __('vehicle-update-image-report.latitude') }}</th>
                    <th class="text-left">{{ __('vehicle-update-image-report.longitude') }}</th>
                    <th class="text-left">{{ __('vehicle-update-image-report.device') }}</th>
                    <th class="text-left">{{ __('vehicle-update-image-report.source') }}</th>
                    <th class="text-left">{{ __('vehicle-update-image-report.type') }}</th> <!-- Cột mới -->
                </tr>
            </thead>

            <tbody>
                @foreach ($odo as $each)
                    @php
                        $imageUrl = $each->minio_url
                            ? env('MINIO_ENDPOINT') . '/' . $each->minio_bucket . '/' . $each->minio_url
                            : null;

                        // Xác định loại dựa trên minio_url
                        $type = 'Other';
                        if (str_contains($each->minio_url ?? '', 'odo-start-day')) {
                            $type = 'Start Day';
                        } elseif (str_contains($each->minio_url ?? '', 'odo-end-day')) {
                            $type = 'End Day';
                        }
                    @endphp
                    <tr>
                        <td class="text-left">
                            @if ($each->minio_url)
                                <a href="{{ $imageUrl }}" target="_blank">
                                    <img src="{{ $imageUrl }}" alt="Image" class="h-16 object-cover" />
                                </a>
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-left">{{ $each->created_at?->format('Y-m-d H:i:s') ?? '-' }}</td>
                        <td class="text-left">{{ $each->latitude ?? '-' }}</td>
                        <td class="text-left">{{ $each->longitude ?? '-' }}</td>
                        <td class="text-left">{{ $each->device->name ?? '-' }}</td>
                        <td class="text-left">{{ $each->source_type ?? '-' }}</td>
                        <td class="text-left">{{ $type }}</td> <!-- Cột loại mới -->
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@stop
