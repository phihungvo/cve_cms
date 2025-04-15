<div class="box h-full p-5 relative">
    <div class="grid grid-cols-3 gap-4 py-5" style="grid-template-columns: repeat(3, 1fr);">
        @if(auth()->user()?->isRoleRoot())
        <div>
            <select class="w-full p-2 border border-gray-300 rounded-lg"
                    wire:change="handleSelectEnterprise(event.target.value)">
                <option value="" selected>-- Select Enterprise --</option>
                @foreach($enterprises as $enterprise)
                    <option value="{{$enterprise->id}}">{{$enterprise->name}}</option>
                @endforeach
            </select>
        </div>
        @else
        <div></div>
        @endif
        <div></div> <!-- Empty middle column -->
        <div>
            <select wire:model="gridLayout"
                    wire:change="$refresh"
                    class="form-select form-select bg-white">
                <option value="3x3">3x3</option>
                <option value="4x4">4x4</option>
                <option value="6x6">6x6</option>
                <option value="8x8">8x8</option>
            </select>
        </div>
    </div>
    <div wire:key="grid-{{ $gridLayout }}">
        <div class="grid" style="
    gap: 2px;
    grid-template-columns: repeat({{ $gridColumns }}, 1fr);
    grid-template-rows: repeat({{ $gridRows }}, auto);
    ">
            {{--        (Debug: {{ json_encode($cameras, JSON_PRETTY_PRINT) }})--}}
            @foreach($cameras as $camera)
                @php
                    //handle hlsUrl
                    $serverCamera = env('SERVER_CAMERA_URL'); // https://hls.aigova.com/ovacam/aigova/{serial}/{instance_id}/stream/render/index.m3u8
                    $hlsUrl = str_replace('{serial}', $camera->serial, $serverCamera);
                    $hlsUrl = str_replace('/{instance_id}', '', $hlsUrl);
                    $hlsUrl = str_replace('/render', '/orginal', $hlsUrl);
                @endphp

                @if($camera->uri)
                    <div
                        class="relative flex flex-col items-center justify-center border border-gray-300 border-2 rounded-md bg-white shadow-lg shadow-blue-500/50 overflow-hidden">
                        <video class="hls-video w-full h-full" controls autoplay
                               onerror="this.style.display='none'; this.nextElementSibling.style.display='block'">
                            <source src="{{ $hlsUrl }}" type="application/x-mpegURL">
                            Your browser does not support the video tag.
                        </video>
                        <div class="absolute inset-0 flex items-center justify-center bg-red-100 text-red-600 hidden"
                             style="display: none;">
                            <span>Video không load được</span>
                        </div>
                        <div class="w-full flex lg:flex-row items-center justify-start gap-1 px-2">
                            <div class="video-status" style="width: 12px;height: 12px;border-radius: 50%; color: green">
                                <svg version="1.1" id="Layer_1" xmlns="http://www.w3.org/2000/svg"
                                     xmlns:xlink="http://www.w3.org/1999/xlink" width="100%" height="100%"
                                     viewBox="0 0 122.88 122.88" enable-background="new 0 0 122.88 122.88"
                                     xml:space="preserve">
                                   <g>
                                       <path fill="currentColor" fill-rule="evenodd" clip-rule="evenodd"
                                             d="M61.438,0c33.93,0,61.441,27.512,61.441,61.441 c0,33.929-27.512,61.438-61.441,61.438C27.512,122.88,0,95.37,0,61.441C0,27.512,27.512,0,61.438,0L61.438,0z"/>
                                   </g>
                               </svg>
                            </div>
                            <div class="camera-name">{{$camera->name}}</div>
                            <div class="flex-1 flex justify-end cursor-pointer py-2 hover:bg-gray-200"
                                 style="cursor: pointer;" wire:click="showCameraDetail({{$camera->id}})">
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
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
        <div class="mt-4 flex justify-center items-center">
            <button wire:click="previousPage" class="btn btn-primary" {{ $page <= 1 ? 'disabled' : '' }}>
                Previous
            </button>
            <span class="mx-2">Page {{ $page }}</span>
            <button wire:click="nextPage"
                    class="btn btn-primary" {{ $page * $perPage >= $list->count() ? 'disabled' : '' }}>
                Next
            </button>
        </div>
    </div>

    <!-- Modal -->
    @if ($showModal && $selectedCamera)
        @php
            //handle hlsUrl
            $serverCamera = env('SERVER_CAMERA_URL'); // https://hls.aigova.com/ovacam/aigova/{serial}/{instance_id}/stream/render/index.m3u8
            $hlsUrl = str_replace('{serial}', $selectedCamera->serial, $serverCamera);
            $hlsUrl = str_replace('/{instance_id}', '', $hlsUrl);
            $hlsUrl = str_replace('/render', '/orginal', $hlsUrl);
        @endphp
        <div class="modal-overlay absolute"
             style="top:0; left:0; right:0; bottom:0; z-index: 100;"
        >
            <div class="modal-content">
                <div class="camera-modal flex flex-col items-center justify-between">
                    <!-- Modal Body -->
                    <div class="camera-modal__body grid grid-cols-2 gap-4 w-full" style="grid-auto-flow: column;grid-template-columns: repeat(2, 1fr); grid-template-rows: repeat(2, 1fr);">
                        <!-- Cell 1: Original live video -->
                        <div class="video-origin border border-gray-300 rounded w-full p-2">
                            <h4 class="text-sm font-bold my-2">Original live</h4>
                            <video width="100%"  class="hls-video mb-4" controls>
                                <source src="{{ $hlsUrl }}" type="application/x-mpegURL">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <!-- Cell 2: AI live video -->
                        <div class="ai-video border border-gray-300 rounded w-full p-2">
                            <h4 class="text-sm font-bold my-2">AI live</h4>
                            <video width="100%" class="hls-video mb-4" controls>
                                <source src="{{ $hlsUrl }}" type="application/x-mpegURL">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <!-- Cell 3: Camera and device information -->
                        <div class="flex flex-col border border-gray-300 rounded w-full h-full">
                            <div class="device-infor p-2">
                                <h3 class="text-sm font-bold my-2">Camera Details</h3>
                                <p><strong>Name:</strong> {{ $selectedCamera->name }}</p>
                                <p><strong>ID:</strong> {{ $selectedCamera->id }}</p>
                                <p><strong>Enterprise:</strong> {{ $selectedCamera->device->enterprise->name ?? 'N/A' }}</p>
                                <p><strong>Device name:</strong> {{ $selectedCamera->device->name ?? 'N/A' }}</p>
                                <p><strong>Status:</strong> {{ $selectedCamera->status ?? 'Active' }}</p>
                            </div>
                            <div class="camera-infor p-2">
                                <h3 class="text-sm font-bold">Camera infor</h3>
                                <p><strong>Name:</strong> {{ $selectedCamera->name }}</p>
                                <p><strong>Model:</strong> {{ $selectedCamera->model }}</p>
                                <p><strong>Serial:</strong> {{ $selectedCamera->serial }}</p>
                            </div>
                        </div>
                        <!-- Cell 4: AI powered config -->
                        <div class="ai-powered-config border border-gray-300 rounded w-full h-full">
                            <h3 class="text-sm py-2">AI Powered config</h3>
                        </div>
                    </div>
                    <!-- Modal Footer -->
                    <button wire:click="closeModal" class="camera-modal__footer my-4 btn btn-secondary">
                        Close
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            console.log('✅ DOM Loaded');
            initializeHLSPlayers();
        });

        window.addEventListener('livewire-video-updated', () => {
            console.log('🔁 Livewire DOM updated');
            setTimeout(() => initializeHLSPlayers(), 750);
        });

        function initializeHLSPlayers() {
            const videos = document.querySelectorAll('.hls-video');

            videos.forEach((video, index) => {
                if (video.dataset.hlsInitialized) return;

                const source = video.querySelector('source');
                if (!source) return;

                const hlsUrl = source.src;

                let statusDiv = video.parentElement.querySelector('.video-status');
                if (!statusDiv) {
                    statusDiv = document.createElement('div');
                    statusDiv.classList.add('video-status');
                    video.parentElement.appendChild(statusDiv)
                }

                if (Hls.isSupported()) {
                    const hls = new Hls();
                    hls.loadSource(hlsUrl);
                    hls.attachMedia(video);
                    hls.on(Hls.Events.ERROR, function (event, data) {
                        if (data.fatal) {
                            console.error(`HLS Error for video ${index}:`, data);

                            // Hiển thị lỗi
                            statusDiv.style.color = 'red';
                        } else {
                            // Thành công
                            statusDiv.style.color = 'green';
                        }
                    });
                } else if (video.canPlayType('application/vnd.apple.mpegurl')) {
                    video.src = hlsUrl;
                }

                video.dataset.hlsInitialized = 'true';
            });
        }
    </script>
@endpush
{{--https://hls.aigova.com/ovacam/aigova/cam04/stream/orginal/index.m3u8--}}
