@extends('domains.device.update-layout')

@section('content')
    <div class="p-0 py-5">
        <div class="box shadow-sm rounded-lg p-4">
            <h2 class="text-lg font-semibold mb-4">{{ __('device-update.device-log') }}</h2>
            <!-- Đường kẻ ngang phân cách tiêu đề và nội dung -->
            <hr class="my-4 border-gray-300">

            @if ($deviceLogs->isEmpty())
                <p class="text-sm text-gray-500">{{ __('device-update.no-logs-available') }}</p>
            @else
                <div class="overflow-x-auto">
                    <table class="table table-bordered mt-2 border-separate border"
                        style="background-color:rgba(240, 240, 240, 0.12);">
                        <thead>
                            <tr>
                                <th class="border-r font-bold text-center">{{ __('device-log.type') }}</th>
                                <th class="border-r font-bold text-center">{{ __('device-log.description') }}</th>
                                <th class="border-r font-bold text-center">{{ __('device-log.created_at') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($deviceLogs as $log)
                                <tr>
                                    <td class="border-r text-center">{{ $log->type }}</td>
                                    <td class="border-r text-center">{{ $log->description ?? 'N/A' }}</td>
                                    <td class="border-r text-center">
                                        {{ \Carbon\Carbon::parse($log->created_at)->format('Y-m-d H:i:s') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
@endsection