@php
    use App\Domains\Device\Enums\DetectedObject;use Carbon\Carbon;
@endphp
@extends('domains.device.rt-analytics-layout')
@section('content-analytics')

    <div class=" intro-y box p-5 mt-5">

        <form method="get" class="mb-5">
            <div class="lg:flex lg:space-x-4">
                <!-- Select Instance -->
                <div class="flex-grow mt-5 lg:mt-0">
                    <x-select name="instance_id" :options="$instances" value="id" text="name"
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
                    <input type="search" name="start_at" value="{{request()->start_at}}"
                           class="form-control form-control-lg cursor-pointer hover:border-gray-600 duration-200"
                           autocomplete="off"
                           placeholder="Start" data-datepicker="" data-datepicker-min-date="">
                </div>

                <!-- Input end data -->
                <div class="flex-grow mt-5 lg:mt-0">
                    <input type="search" name="end_at" value="{{request()->end_at}}"
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
                        <div class="flex gap-1 items-center">
                            @if($item->detected_object == DetectedObject::PERSON->value)
                                {{--                            icon person--}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-person-bounding-box" viewBox="0 0 16 16">
                                    <path d="M1.5 1a.5.5 0 0 0-.5.5v3a.5.5 0 0 1-1 0v-3A1.5 1.5 0 0 1 1.5 0h3a.5.5 0 0 1 0 1zM11 .5a.5.5 0 0 1 .5-.5h3A1.5 1.5 0 0 1 16 1.5v3a.5.5 0 0 1-1 0v-3a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 1-.5-.5M.5 11a.5.5 0 0 1 .5.5v3a.5.5 0 0 0 .5.5h3a.5.5 0 0 1 0 1h-3A1.5 1.5 0 0 1 0 14.5v-3a.5.5 0 0 1 .5-.5m15 0a.5.5 0 0 1 .5.5v3a1.5 1.5 0 0 1-1.5 1.5h-3a.5.5 0 0 1 0-1h3a.5.5 0 0 0 .5-.5v-3a.5.5 0 0 1 .5-.5"/>
                                    <path d="M3 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm8-9a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
                                </svg>
                            @elseif($item->detected_object == DetectedObject::VEHICLE->value)
                                {{--                                icon vehicle--}}
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bus-front-fill" viewBox="0 0 16 16">
                                    <path d="M16 7a1 1 0 0 1-1 1v3.5c0 .818-.393 1.544-1 2v2a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5V14H5v1.5a.5.5 0 0 1-.5.5h-2a.5.5 0 0 1-.5-.5v-2a2.5 2.5 0 0 1-1-2V8a1 1 0 0 1-1-1V5a1 1 0 0 1 1-1V2.64C1 1.452 1.845.408 3.064.268A44 44 0 0 1 8 0c2.1 0 3.792.136 4.936.268C14.155.408 15 1.452 15 2.64V4a1 1 0 0 1 1 1zM3.552 3.22A43 43 0 0 1 8 3c1.837 0 3.353.107 4.448.22a.5.5 0 0 0 .104-.994A44 44 0 0 0 8 2c-1.876 0-3.426.109-4.552.226a.5.5 0 1 0 .104.994M8 4c-1.876 0-3.426.109-4.552.226A.5.5 0 0 0 3 4.723v3.554a.5.5 0 0 0 .448.497C4.574 8.891 6.124 9 8 9s3.426-.109 4.552-.226A.5.5 0 0 0 13 8.277V4.723a.5.5 0 0 0-.448-.497A44 44 0 0 0 8 4m-3 7a1 1 0 1 0-2 0 1 1 0 0 0 2 0m8 0a1 1 0 1 0-2 0 1 1 0 0 0 2 0m-7 0a1 1 0 0 0 1 1h2a1 1 0 1 0 0-2H7a1 1 0 0 0-1 1"/>
                                </svg>
                            @elseif($item->detected_object == DetectedObject::ANIMAL->value)
                                {{--                            icon animal--}}

                                <svg width="16px" height="16px" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">

                                    <g id="SVGRepo_bgCarrier" stroke-width="0"/>

                                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"/>

                                    <g id="SVGRepo_iconCarrier">
                                        <path
                                            d="M13.2491 2.00002L16.4346 2C16.9438 2 17.442 2.14135 17.8752 2.40794L20.6431 4.11125C20.8649 4.24775 21 4.48955 21 4.75V6.25C21 7.49264 19.9926 8.5 18.75 8.5H18.5V17.5469C19.497 17.6807 20.1945 18.1015 20.5946 18.8224C20.8179 19.2247 20.9126 19.6625 20.9569 20.0651C21.0001 20.4561 21 20.8643 21 21.2247V21.25C21 21.6642 20.6642 22 20.25 22H16.2508H5.83333C3.71624 22 2 20.2838 2 18.1667C2 16.7686 2.74891 15.5462 3.86385 14.8774C4.21907 14.6644 4.67975 14.7796 4.89281 15.1348C5.10587 15.49 4.99062 15.9507 4.63541 16.1638C3.95359 16.5727 3.5 17.3171 3.5 18.1667C3.5 19.4553 4.54467 20.5 5.83333 20.5C6.15188 20.5 6.34997 20.4168 6.48162 20.3242C6.62082 20.2262 6.72963 20.0859 6.81348 19.9168C6.89798 19.7464 6.94675 19.5671 6.97352 19.4243C6.98658 19.3546 6.99358 19.2981 6.9972 19.2617C6.99876 19.246 6.99967 19.2343 7.00015 19.2273L7.00059 19.1925C7.00115 19.1586 7.00224 19.1104 7.00436 19.0493C7.00859 18.9273 7.01695 18.7535 7.03348 18.5397C7.06646 18.1132 7.13235 17.5223 7.26464 16.8627C7.52372 15.5707 8.06051 13.8945 9.21612 12.7232C10.0641 11.8638 10.525 10.6045 10.7634 9.35923C10.9991 8.12815 11 7.00801 11 6.5V4.25002C11 3.00796 12.0059 2.00003 13.2491 2.00002ZM7.75 19.25C8.49958 19.275 8.49956 19.2757 8.49956 19.2757L8.49948 19.2781L8.49932 19.2819L8.49888 19.2919L8.49726 19.3205C8.4958 19.3433 8.49348 19.3735 8.48984 19.4101C8.48259 19.4831 8.46993 19.5829 8.44783 19.7007C8.40796 19.9134 8.33458 20.2001 8.19703 20.5H15.4473C15.3561 20.0064 15.1078 19.4939 14.4335 19.1799C14.4224 19.1748 14.4115 19.1694 14.4008 19.1637C14.3486 19.1363 14.1994 19.0871 13.9516 19.0479C13.7216 19.0116 13.4674 18.9931 13.25 18.9931C13.1964 18.9931 13.0783 18.9947 12.9672 18.9965L12.8256 18.9988L12.7665 18.9999L12.7655 18.9999C12.3513 19.0075 12.0095 18.6779 12.0019 18.2637C11.9943 17.8496 12.3239 17.5077 12.738 17.5001L12.7995 17.499L12.9435 17.4966C13.0511 17.4949 13.1822 17.4931 13.25 17.4931C13.4846 17.4931 13.7453 17.5086 14 17.5402V15.2528C14 14.8386 14.3358 14.5028 14.75 14.5028C15.1642 14.5028 15.5 14.8386 15.5 15.2528V18.0573C16.5648 18.7386 16.8739 19.7599 16.9635 20.5H19.4875C19.4825 20.407 19.4756 20.317 19.466 20.2294C19.4328 19.9286 19.3719 19.7103 19.2831 19.5504C19.153 19.3159 18.8498 19 17.75 19C17.3358 19 17 18.6642 17 18.25V7.75C17 7.33579 17.3358 7 17.75 7H18.75C19.1642 7 19.5 6.66421 19.5 6.25V5.16909L17.089 3.68543C16.8918 3.56405 16.6657 3.5 16.4346 3.5L13.2491 3.50002C12.8355 3.50002 12.5 3.83523 12.5 4.25002V6.48171C12.5128 6.71048 12.592 6.90709 12.7051 7.03567C12.8062 7.15046 12.9658 7.25 13.25 7.25C13.5368 7.25 13.7027 7.14866 13.8057 7.031C13.9206 6.89967 14 6.69587 14 6.45C14 6.03579 14.3358 5.7 14.75 5.7C15.1642 5.7 15.5 6.03579 15.5 6.45C15.5 7.00412 15.3227 7.57533 14.9343 8.01899C14.534 8.47634 13.9499 8.75 13.25 8.75C12.938 8.75 12.6513 8.69566 12.3945 8.59601C12.3568 8.92795 12.3058 9.27987 12.2366 9.64132C11.975 11.0074 11.4359 12.6092 10.2839 13.7768C9.43949 14.6326 8.97628 15.9563 8.73536 17.1576C8.61765 17.7446 8.55854 18.2736 8.52902 18.6554C8.5143 18.8458 8.50703 18.9982 8.50346 19.1013C8.50167 19.1529 8.5008 19.192 8.50039 19.2173L8.50004 19.2446L8.5 19.25C8.5 19.2582 8.49983 19.2675 8.49956 19.2757L7.75 19.25Z"
                                            fill="#212121"/>
                                    </g>

                                </svg>
                            @else
                                {{--                            icon unknow--}}
                                <svg width="16px" height="16px" viewBox="0 0 24 24" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M2 20V4C2 3.44772 2.44772 3 3 3H8.44792C8.79153 3 9.11108 3.17641 9.29416 3.46719L10.5947 5.53281C10.7778 5.82359 11.0974 6 11.441 6H21C21.5523 6 22 6.44772 22 7V20C22 20.5523 21.5523 21 21 21H3C2.44772 21 2 20.5523 2 20Z"
                                        stroke="#200E32" stroke-width="2"/>
                                    <path
                                        d="M10.1833 11.5697C10.2383 11.1301 10.4368 10.7209 10.7481 10.4056C11.0594 10.0903 11.466 9.8865 11.9049 9.82585C12.3438 9.7652 12.7904 9.85107 13.1755 10.0701C13.5607 10.2892 13.8627 10.6292 14.0349 11.0375C14.2071 11.4457 14.2397 11.8994 14.1278 12.3281C14.0159 12.7568 13.7656 13.1365 13.4159 13.4085C13.2977 13.5004 13.1508 13.5782 12.991 13.6411C12.4794 13.8425 12.0212 14.2761 12.0212 14.8259L12.0212 14.9733"
                                        stroke="#200E32" stroke-width="2" stroke-linecap="round"/>
                                    <circle cx="12.0212" cy="17.2707" r="0.91897" fill="#200E32"/>
                                </svg>
                            @endif
                            <span>{{$item->detected_object}}</span>
                        </div>
                        <span>{{$createdAt}}</span>
                    </div>
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-lg ">{{ $item->event_type }}</span>
                        <span class="text-lg ">{{ $item->instanceRule->name }}</span>
                    </div>

                    <div class="overflow-hidden mb-2" style="aspect-ratio: 1/1; width: 100%;">
                        <img onclick="showModalVideo('{{$item->video_url}}')"
                             class="w-full h-full object-cover mb-2 cursor-pointer"
                             src="{{$item->image_url}}" alt="Hình ảnh"/>
                    </div>
                    <div class="">
                        <span class="text-lg">{{$item->event_name}}</span>
                        <p>{{$item->event_value}}</p>
                    </div>

                </div>
            @endforeach
        </div>

    </div>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        function showModalVideo(videoUrl) {
            Swal.fire({
                html: `
                    <video controls autoplay class="w-full h-full object-cover">
                        <source src="${videoUrl}" type="video/mp4">
                    </video>
                `,
                backdrop: `
                    rgba(255,255,255,0.7)
                `,
                showCloseButton: false,
                showCancelButton: false,
                focusConfirm: false,
                confirmButtonText: 'Close',
                confirmButtonColor: '#0d6efd',
            });
        }


    </script>
@endpush
