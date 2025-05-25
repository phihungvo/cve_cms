@extends('domains.device.update-layout')

@section('content')
    <div class="intro-y box p-5 mt-5">
        <h2 class="text-lg font-medium mb-5">{{ __('rt-analytics-input-source.rules') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="col-span-1 w-full ">
                <!-- Content for the first column (1/3) -->
                <ul class="flex flex-col justify-between gap-1 h-full">
                    <li class="btn btn-outline-secondary py-4">Intrusion detection</li>
                    <li class="btn btn-outline-secondary py-4">Area Enter/Exit</li>
                    <li class="btn btn-outline-secondary py-4">Loitering</li>
                    <li class="btn btn-outline-secondary py-4">Crowding</li>
                    <li class="btn btn-outline-secondary py-4">Line Crossing</li>
                </ul>
            </div>
            <div class="col-span-2 w-full">
                <!-- Content for the second column (2/3) -->
                <!-- content left -->
                <div class="w-full h-full flex flex-col border border-gray-300 rounded-md p-2">
                    <!-- rules name -->
                    <h2 class="text-sm font-bold py-2 w-full">Rule&nbsp;Name</h2>
                    <div class="w-full h-full grid grid-cols-1 md:grid-cols-2 gap-2">

                        <div class="col-span-1">
                            <input class="border border-gray-300 rounded-lg w-full p-2 text-sm" type="text"
                                   name="rule_name"
                                   id="rule_name" placeholder="Enter rules name">
                            <h3 class="py-2">Object types to detect</h3>
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="object_type" id="object_type_person">
                                    <label class="cursor-pointer" for="object_type_person">Person</label>
                                </div>
                                <div class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="object_type" id="object_type_car">
                                    <label class="cursor-pointer" for="object_type_car">Car</label>
                                </div>
                                <div class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="object_type" id="object_type_truck">
                                    <label class="cursor-pointer p-1" for="object_type_truck">Truck</label>
                                </div>
                                <div class="flex items-center gap-2">
                                    <input class="cursor-pointer" type="checkbox" name="object_type"
                                           id="object_type_unknow">
                                    <label for="object_type_unknow" class="cursor-pointer">Unknow</label>
                                </div>
                            </div>
                        </div>
                        <!-- view camera -->
                        <div class="col-span-1 border p-2 ">
                            <div class="w-full h-full ">
                                <h3>live view camera</h3>
                                <video class="hls-video" width="100%" height="400" controls autoplay>
                                    <source src="{{$instance->input_source}}"
                                            type="application/x-mpegURL">
                                    Your browser does not support the video tag.
                                </video>
                                {{--                                <video class="hls-video" controls autoplay>--}}
                                {{--                                    <source src="http://localhost:8000/output.m3u8" type="application/vnd.apple.mpegurl">--}}
                                {{--                                </video>--}}
                                <button type="button" class="p-2 cursor-pointer" onclick="showUiDraw()">
                                    <svg fill="#000000" height="16px" width="16px" version="1.1" id="Capa_1"
                                         xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                         viewBox="0 0 469 469" xml:space="preserve">
                                        <g>
                                            <g>
                                                <path d="M455.5,0h-442C6,0,0,6,0,13.5v211.9c0,7.5,6,13.5,13.5,13.5s13.5-6,13.5-13.5V27h415v415H242.4c-7.5,0-13.5,6-13.5,13.5
                                                s6,13.5,13.5,13.5h213.1c7.5,0,13.5-6,13.5-13.5v-442C469,6,463,0,455.5,0z"/>
                                                <path d="M175.6,279.9H13.5c-7.5,0-13.5,6-13.5,13.5v162.1C0,463,6,469,13.5,469h162.1c7.5,0,13.5-6,13.5-13.5V293.4
                                                C189.1,286,183,279.9,175.6,279.9z M162.1,442H27V306.9h135.1V442z"/>
                                                <path d="M360.4,127.7v71.5c0,7.5,6,13.5,13.5,13.5s13.5-6,13.5-13.5V95.1c0-7.5-6-13.5-13.5-13.5H269.8c-7.5,0-13.5,6-13.5,13.5
                                                s6,13.5,13.5,13.5h71.5L212.5,237.4c-5.3,5.3-5.3,13.8,0,19.1c2.6,2.6,6.1,4,9.5,4s6.9-1.3,9.5-4L360.4,127.7z"/>
                                            </g>
                                        </g>
                                </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- content right -->

        </div>
    </div>
@stop

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.0/fabric.min.js"></script>
    <!-- Thêm HLS.js từ CDN -->
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    {{--    <script>--}}
    {{--        document.addEventListener('DOMContentLoaded', function () {--}}
    {{--            const videos = document.querySelectorAll('.hls-video');--}}

    {{--            videos.forEach((video, index) => {--}}
    {{--                const source = video.querySelector('source');--}}
    {{--                if (!source) return;--}}

    {{--                const hlsUrl = source.src;--}}

    {{--                if (Hls.isSupported()) {--}}
    {{--                    const hls = new Hls();--}}
    {{--                    hls.loadSource(hlsUrl);--}}
    {{--                    hls.attachMedia(video);--}}
    {{--                    hls.on(Hls.Events.ERROR, function (event, data) {--}}
    {{--                        if (data.fatal) {--}}
    {{--                            console.error(`HLS Error for video ${index}:`, data);--}}
    {{--                            hls.destroy(); // optional: cleanup--}}
    {{--                        }--}}
    {{--                    });--}}
    {{--                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {--}}
    {{--                    // Safari (native support)--}}
    {{--                    video.src = hlsUrl;--}}
    {{--                } else {--}}
    {{--                    console.warn(`HLS is not supported in this browser for video ${index}`);--}}
    {{--                }--}}
    {{--            });--}}
    {{--        });--}}
    {{--    </script>--}}
    <script>
        var initialCanvasData = @json($instance->lines ?? null);
        const url = '{{ route('device.runtime-analytics.analytcs-rules', ['id' => $row->id,
    'instanceId' => $instance->id]) }}';
    </script>


    <script src="{{ asset('js/rt-analytics-rule.js') }}"></script>
@endpush
