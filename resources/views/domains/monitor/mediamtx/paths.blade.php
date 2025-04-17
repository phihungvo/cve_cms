@extends('layouts.in')

@section('body')

<div class="box lg:flex items-stretch mb-6">
    <div class="border-b lg:border-b-0 lg:border-r border-slate-200/60 flex items-center">
        <h2 class="text-base font-medium p-5">
            {{ __('mediamtx-paths.meta-title') }}
        </h2>
    </div>

    <div class="flex-1 p-5">
        @if (isset($paths) && count($paths) > 0)
        <div class="text-lg font-medium leading-none">
            {{ __('mediamtx-paths.total-paths') }}: {{ count($paths) }}
        </div>
        @else
        <div class="text-lg font-medium leading-none text-danger">
            {{ __('mediamtx-paths.no-paths-available') }}
        </div>
        @endif
    </div>
</div>

<div class="box">
    @if (isset($paths) && count($paths) > 0)
    @foreach ($paths as $index => $path)
    <div class="border-b border-slate-200/60">
        <h2 class="text-base font-medium p-5">
            <button type="button" class="w-full text-left flex items-center" onclick="toggleDropdown('path-{{ $index }}')">
                <span class="flex-1">{{ $path['name'] }}</span>
                <svg class="w-5 h-5 transform transition-transform duration-200" id="path-{{ $index }}-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
            </button>
        </h2>

        <div id="path-{{ $index }}" class="p-5 hidden">
            <!-- General Settings -->
            <div class="mb-5">
                <h3 class="text-lg font-medium mb-3">{{ __('mediamtx-paths.general-settings') }}</h3>
                <div class="mb-3">
                    <div class="flex">
                        <div class="flex-1 font-medium">{{ __('mediamtx-paths.conf-name') }}</div>
                        <div class="text-slate-500">{{ $path['confName'] }}</div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="flex">
                        <div class="flex-1 font-medium">{{ __('mediamtx-paths.ready') }}</div>
                        <div class="text-slate-500">{{ $path['ready'] ? 'Yes' : 'No' }}</div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="flex">
                        <div class="flex-1 font-medium">{{ __('mediamtx-paths.ready-time') }}</div>
                        <div class="text-slate-500">{{ $path['readyTime'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Source Settings -->
            <div class="mb-5">
                <h3 class="text-lg font-medium mb-3">{{ __('mediamtx-paths.source-settings') }}</h3>
                <div class="mb-3">
                    <div class="flex">
                        <div class="flex-1 font-medium">{{ __('mediamtx-paths.source-type') }}</div>
                        <div class="text-slate-500">{{ $path['source']['type'] }}</div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="flex">
                        <div class="flex-1 font-medium">{{ __('mediamtx-paths.source-id') }}</div>
                        <div class="text-slate-500">{{ $path['source']['id'] }}</div>
                    </div>
                </div>
            </div>

            <!-- Stream Settings -->
            <div class="mb-5">
                <h3 class="text-lg font-medium mb-3">{{ __('mediamtx-paths.stream-settings') }}</h3>
                <div class="mb-3">
                    <div class="flex">
                        <div class="flex-1 font-medium">{{ __('mediamtx-paths.tracks') }}</div>
                        <div class="text-slate-500">{{ implode(', ', $path['tracks']) }}</div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="flex">
                        <div class="flex-1 font-medium">{{ __('mediamtx-paths.bytes-received') }}</div>
                        <div class="text-slate-500">{{ number_format($path['bytesReceived']) }} bytes</div>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="flex">
                        <div class="flex-1 font-medium">{{ __('mediamtx-paths.bytes-sent') }}</div>
                        <div class="text-slate-500">{{ number_format($path['bytesSent']) }} bytes</div>
                    </div>
                </div>
            </div>

            <!-- HLS Stream -->
            <div class="mb-5">
                <h3 class="text-lg font-medium mb-3">{{ __('mediamtx-paths.hls-stream') }}</h3>
                <div class="mb-3">
                    <video id="hls-video-{{ $index }}" class="w-full" controls>
                        <source src="https://hls.aigova.com/{{ $path['name'] }}/index.m3u8" type="application/x-mpegURL">
                        Your browser does not support the video tag.
                    </video>
                </div>
            </div>
            <!-- Readers -->
            <div class="mb-5">
                <h3 class="text-lg font-medium mb-3">{{ __('mediamtx-paths.readers') }}</h3>
                @if (count($path['readers']) > 0)
                @foreach ($path['readers'] as $reader)
                <div class="mb-3">
                    <div class="flex">
                        <div class="flex-1 font-medium">{{ __('mediamtx-paths.reader-type') }}</div>
                        <div class="text-slate-500">{{ $reader['type'] }}</div>
                    </div>
                    <div class="flex mt-2">
                        <div class="flex-1 font-medium">{{ __('mediamtx-paths.reader-id') }}</div>
                        <div class="text-slate-500">{{ $reader['id'] ?: 'N/A' }}</div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="text-slate-500">{{ __('mediamtx-paths.no-readers') }}</div>
                @endif
            </div>
        </div>
    </div>
    @endforeach
    @else
    <div class="p-5 text-lg font-medium leading-none text-danger">
        {{ __('mediamtx-paths.no-paths-available') }}
    </div>
    @endif
</div>

@push('scripts')
<!-- Thêm HLS.js từ CDN -->
<script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
<script>
    function toggleDropdown(id) {
        const element = document.getElementById(id);
        const icon = document.getElementById(id + '-icon');
        element.classList.toggle('hidden');
        icon.classList.toggle('rotate-180');
    }

    // Khởi tạo HLS player cho từng video
    document.addEventListener('DOMContentLoaded', function () {
        @foreach ($paths as $index => $path)
            const video{{ $index }} = document.getElementById('hls-video-{{ $index }}');
            const hlsUrl{{ $index }} = 'https://hls.cvedix.com/{{ $path['name'] }}/index.m3u8';

            if (Hls.isSupported()) {
                const hls{{ $index }} = new Hls();
                hls{{ $index }}.loadSource(hlsUrl{{ $index }});
                hls{{ $index }}.attachMedia(video{{ $index }});
                hls{{ $index }}.on(Hls.Events.ERROR, function (event, data) {
                    if (data.fatal) {
                        console.error('HLS Error for {{ $path['name'] }}:', data);
                    }
                });
            } else if (video{{ $index }}.canPlayType('application/vnd.apple.mpegurl')) {
                video{{ $index }}.src = hlsUrl{{ $index }};
            }
        @endforeach
    });
</script>
@endpush

@stop