@php
    use Carbon\Carbon;
@endphp
@extends('domains.device.rt-analytics-layout')
@section('content-analytics')

    <div class=" intro-y box p-5 mt-5">

        <form method="get" class="mb-5">
            <div class="lg:flex lg:space-x-4">
                <!-- Select Instance -->
                <div class="flex-grow mt-5 lg:mt-0">
                    <x-select name="instance_id" :options="$instances" value="id" text="instance_name"
                              class="form-control form-control-lg cursor-pointer hover:border-gray-600 duration-200"
                              placeholder="{{__('--Select Instance --')}}" data-change-submit></x-select>
                </div>
                <!-- Select Rule -->
                <div class="flex-grow mt-5 lg:mt-0">
                    <x-select name="rule_id" :options="$rules" value="id" text="name"
                              class="form-control form-control-lg cursor-pointer hover:border-gray-600 duration-200"
                              placeholder="{{__('--Select Rule --')}}" data-change-submit></x-select>
                </div>

                <!-- Select Rule Type -->
                <div class="flex-grow mt-5 lg:mt-0">
                    <x-select name="rule_type" :options="$ruleTypes" value="name" text="value"
                              class="form-control form-control-lg cursor-pointer hover:border-gray-600 duration-200"
                              placeholder="{{__('--Select Rule Type --')}}" data-change-submit></x-select>
                </div>

                <!-- Select Detected Object -->
                <div class="flex-grow mt-5 lg:mt-0">
                    <x-select name="detected_object" :options="$detectedObjects" value="name" text="value"
                              class="form-control form-control-lg cursor-pointer hover:border-gray-600 duration-200"
                              placeholder="{{__('--Select Detected Object --')}}" data-change-submit></x-select>
                </div>

                <!-- Input start date -->
                <div class="flex-grow mt-5 lg:mt-0">
                    <input type="search" name="start_at" value=""
                           class="form-control form-control-lg cursor-pointer hover:border-gray-600 duration-200"
                           autocomplete="off"
                           placeholder="Start" data-datepicker="en" data-datepicker-min-date="">
                </div>

                <!-- Input end data -->
                <div class="flex-grow mt-5 lg:mt-0">
                    <input type="search" name="end_at" value=""
                           class="form-control form-control-lg cursor-pointer hover:border-gray-600 duration-200"
                           autocomplete="off"
                           placeholder="End" data-datepicker="" data-datepicker-min-date="">
                </div>
                <!-- btn Send -->
                <div class="flex-grow lg:ml-4 mt-2 mt-5 lg:mt-0 bg-white">
                    <button type="submit" class="btn hover:bg-gray-400 hover:text-white form-control-lg
                     whitespace-nowrap w-full cursor-pointer hover:border-gray-600 duration-200">Send
                    </button>
                </div>
            </div>
        </form>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4 mt-5 lg:mt-0">
            @foreach($events as $item)
                @php
                    $createdAt = isset($item->created_at)
                    ? Carbon::parse($item->created_at)->setTimezone('Asia/Ho_Chi_Minh') : 'N/A';
                @endphp
                <div class="flex flex-col justify-center p-2 border border-gray-500 rounded-sm bg-white
                shadow-md hover:shadow-xl hover:scale-105 duration-200">
                    <div class="flex justify-between items-center mb-2">
                        <span>{{$item->detected_object}}</span>
                        <span>{{$createdAt}}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-lg ">{{ $item->instanceRule->rule_type }}</span>
                        <span class="text-lg ">{{ $item->instanceRule->name }}</span>
                    </div>

                    <div class="overflow-hidden mb-2" style="aspect-ratio: 16/9; width: 100%;">
                        <img class="w-full h-full object-cover mb-2" src="{{$item->image_url}}" alt="Hình ảnh"/>
                    </div>
                    <div class="">
                        <span class="text-lg" >{{$item->event_name}}</span>
                        <p>{{$item->event_value}}</p>
                    </div>

                </div>
            @endforeach
        </div>

    </div>


    {{--    <div class="intro-y box p-5 mt-5">--}}
    {{--        <!-- Display Success or Error Messages -->--}}
    {{--        <h2 class="text-lg font-medium mb-5">{{ __('rt-analytics-index.all-instance-list') }}</h2>--}}


    {{--    </div>--}}
@endsection
@push('styles')
    <style>
        .status-indicator {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
        }

        .status-indicator.pending {
            background-color: #ffa500; /* Orange cho trạng thái Pending */
            color: white;
        }

        .status-indicator.online {
            background-color: #28a745; /* Green cho trạng thái Online */
            color: white;
        }

        .status-indicator.offline {
            background-color: #dc3545; /* Red cho trạng thái Offline */
            color: white;
        }
    </style>
@endpush
@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/hls.js@latest"></script>
    <script>
        function showToast(message) {
            const toast = document.createElement('div');
            toast.textContent = message;
            toast.style.position = 'fixed';
            toast.style.bottom = '40px';
            toast.style.right = '20px';
            toast.style.backgroundColor = '#4caf50';
            toast.style.color = '#fff';
            toast.style.padding = '10px 20px';
            toast.style.borderRadius = '5px';
            toast.style.boxShadow = '0 2px 5px rgba(0, 0, 0, 0.3)';
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(20px)';
            toast.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            document.body.appendChild(toast);

            requestAnimationFrame(() => {
                toast.style.opacity = '1';
                toast.style.transform = 'translateY(0)';
            });

            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateY(20px)';
                toast.addEventListener('transitionend', () => document.body.removeChild(toast));
            }, 3000);
        }

        function copyToClipboard(button) {
            const uuid = button.getAttribute('data-uuid');
            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(uuid).then(() => {
                    showToast('UUID copied to clipboard successfully!');
                }).catch(err => {
                    console.error('Failed to copy UUID: ', err);
                });
            } else {
                const tempInput = document.createElement('textarea');
                tempInput.value = uuid;
                document.body.appendChild(tempInput);
                tempInput.select();
                try {
                    document.execCommand('copy');
                    showToast('UUID copied to clipboard successfully!');
                } catch (err) {
                    console.error('Fallback copy failed: ', err);
                    alert('Copy to clipboard is not supported in your browser. Please copy manually.');
                }
                document.body.removeChild(tempInput);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            console.log('DOM Loaded');
            initializeHLSPlayer();
        });

        function initializeHLSPlayer() {
            const rowElements = document.querySelectorAll('.input-source');
            const urls = Array.from(rowElements).map(row => row.textContent.trim());

            console.log('URLs:', urls);

            urls.forEach((url, index) => {
                const statusEl = document.querySelector(`.status-indicator[data-input-source="${url}"]`);

                if (!statusEl) {
                    console.error(`Status element not found for URL: ${url}`);
                    return;
                }

                // Đặt trạng thái mặc định là pending
                statusEl.className = 'status-indicator pending';
                statusEl.textContent = 'Pending';

                // Kiểm tra loại URL
                if (url.endsWith('.m3u8') && Hls.isSupported()) {
                    // Xử lý HLS
                    const hls = new Hls();
                    hls.loadSource(url);

                    hls.on(Hls.Events.MANIFEST_PARSED, () => {
                        console.log(`HLS video ${index + 1} connected successfully: ${url}`);
                        statusEl.className = 'status-indicator online';
                        statusEl.textContent = 'Online';
                    });

                    hls.on(Hls.Events.ERROR, (event, data) => {
                        console.error(`HLS Error for video ${index + 1}:`, data);
                        statusEl.className = 'status-indicator offline';
                        statusEl.textContent = 'Offline';
                    });
                } else if (url.startsWith('rtsp://')) {
                    // Xử lý RTSP qua API
                    checkRTSPStatus(url, statusEl);
                } else {
                    // URL không hợp lệ
                    console.warn(`Unsupported URL format: ${url}`);
                    statusEl.className = 'status-indicator offline';
                    statusEl.textContent = 'Offline';
                }
            });
        }

        function checkRTSPStatus(url, statusEl) {
            console.log(window.location.href);
            fetch("{{route('device.check-rtsp-status')}}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({_action: 'checkRTSPStatus', url: url})
            })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        console.log(`RTSP URL ${url} connected successfully.`);
                        statusEl.className = 'status-indicator online';
                        statusEl.textContent = 'Online';
                    } else {
                        console.error(`RTSP URL ${url} failed:`, data.message);
                        statusEl.className = 'status-indicator offline';
                        statusEl.textContent = 'Offline';
                    }
                })
                .catch(error => {
                    console.error(`Error checking RTSP URL ${url}:`, error);
                    statusEl.className = 'status-indicator offline';
                    statusEl.textContent = 'Offline';
                });
        }
    </script>
@endpush
