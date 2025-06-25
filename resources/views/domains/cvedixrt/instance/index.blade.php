@php use Illuminate\Support\Carbon; @endphp
@extends('layouts.in')

@section('body')
    <div class="intro-y box p-5 mt-5">
        <form method="get">
            <div class="sm:flex sm:space-x-4 justify-between items-center py-2">
                <!--Input search -->
                <div class="flex-grow mt-2 sm:mt-0">
                    <input type="search" name="search" id="search"
                           class="form-control form-select-lg px-2"
                           placeholder="{{__('cvedixrt-instance-index.filter')}}"
                           data-table-search="#device-cvedix-instance-list-table">
                </div>
                <!--Select Filter Solution-->
                <div class="sm:ml-4 mt-2 sm:mt-0">
                    <x-select name="cvedixrt_solution_id"
                              :options="$solutions" class="cursor-pointer"
                              value="id" text="name"
                              placeholder="{{__('cvedixrt-instance-index.filter-solutions')}}"
                              data-change-submit></x-select>
                </div>
                <!--Select Filter Group-->
                <div class="sm:ml-4 mt-2 sm:mt-0">
                    <x-select name="cvedixrt_group_id" class="cursor-pointer"
                              :options="$groups"
                              value="id" text="name"
                              placeholder="{{__('cvedixrt-instance-index.filter-groups')}}"
                              data-change-submit></x-select>
                </div>

                <!--Btn create instance-->
                <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
                    <a class="btn form-control-lg whitespace-nowrap"
                       href="{{route('cvedixrt_instance.create')}}">
                        {{__('cvedixrt-instance-index.create-instance')}}
                    </a>
                </div>
            </div>
        </form>

        {{-- Table show list instance --}}
        <div class="overflow-auto scroll-visible header-sticky">
            <table id="device-cvedix-instance-list-table"
                   class="table sm:mt-2 font-medium font-semibold text-center whitespace-nowrap"
                   data-table-sort
                   data-table-pagination data-table-pagination-limit="10">
                <thead>
                <tr>
                    <th class="" style="display: none">{{ __('uuid') }}</th>
                    <th class="w-1">{{ __('cvedixrt-instance-index.no') }}</th>
                    <th class="text-center w-1">{{ __('cvedixrt-instance-index.instance-id') }}</th>
                    <th class="text-left w-1">{{ __('cvedixrt-instance-index.name') }}</th>
                    <th class="text-left w-1">{{ __('cvedixrt-instance-index.source') }}</th>
                    <th class="text-left w-1">{{ __('cvedixrt-instance-index.solutions') }}</th>
                    <th class="text-left w-1">{{ __('cvedixrt-instance-index.groups') }}</th>
                    <th class="text-center w-1">{{ __('cvedixrt-instance-index.events') }}</th>
                </tr>
                </thead>
                <tbody>
                @foreach($list as $index => $instance)
                    @php
                        $link = route('cvedixrt_instance.index');
                         $link = route('cvedixrt_instance.update', $instance->id);
                         $updateAt = isset($instance->updated_at)
                             ? \Carbon\Carbon::parse($instance->updated_at)->setTimezone('Asia/Ho_Chi_Minh')
                             : '-';
                    @endphp
                    <tr>
                        <!-- UUID hidden -->
                        <td style="display: none">
                            <a href="{{$link}}" class="block">{{$instance->uuid}}</a>
                        </td>
                        <!-- No -->
                        <td>
                            <a href="{{$link}}" class="block">{{$index + 1}}</a>
                        </td>
                        <!-- ID -->
                        <td class="text-center">
                            <a style="display: none" href="{{$link}}">{{$instance->id}}</a>
                            <a href="javascript:void(0);" onclick="copyToClipboard(this);" class="ml-2"
                               data-uuid="{{$instance->uuid}}"
                               style="display: inline-block;height: 16px; width: 16px;">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 115.77 122.88" class="icon-svg">
                                    <style>.st0 {
                                            fill-rule: evenodd;
                                            clip-rule: evenodd;
                                        }</style>
                                    <g>
                                        <path class="st0"
                                              d="M89.62,13.96v7.73h12.19h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02v0.02v73.27v0.01h-0.02c-0.01,3.84-1.57,7.33-4.1,9.86c-2.51,2.5-5.98,4.06-9.82,4.07v0.02h-0.02h-61.7H40.1v-0.02c-3.84-0.01-7.34-1.57-9.86-4.1c-2.5-2.51-4.06-5.98-4.07-9.82h-0.02v-0.02V92.51H13.96h-0.01v-0.02c-3.84-0.01-7.34-1.57-9.86-4.1c-2.5-2.51-4.06-5.98-4.07-9.82H0v-0.02V13.96v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07V0h0.02h61.7h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02V13.96L89.62,13.96z M79.04,21.69v-7.73v-0.02h0.02c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v64.59v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h12.19V35.65v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07v-0.02h0.02H79.04L79.04,21.69z M105.18,108.92V35.65v-0.02h0.02c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v73.27v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h61.7h0.02v0.02c0.91,0,1.75-0.39,2.37-1.01c0.61-0.61,1-1.46,1-2.37h-0.02V108.92L105.18,108.92z"/>
                                    </g>
                                </svg>
                            </a>
                        </td>
                        <!-- Name -->
                        <td class="text-left">
                            <a href="{{$link}}" class="block">{{$instance->name}}</a>
                        </td>
                        <!-- Source -->
                        <td class="text-left">
                            <a
                                style="max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"
                                href="{{$link}}"
                                title="{{$instance->input_source ?? '-'}}"
                                class="block text-ellipsis">{{$instance->source ?? '-'}}</a>
                        </td>
                        <!-- Solutions -->
                        <td class="text-left">
                            <a href="{{$link}}" class="block">{{$instance->solution->name ?? '-'}}</a>
                        </td>
                        <!-- Groups -->
                        <td class="text-left">
                            <a href="{{$link}}" class="block">{{$instance->group->name ?? '-'}}</a>
                        </td>
                        <!-- Start/Stop -->
                        <td class="text-center" x-data="{ eventsCount: {{$instance->events_count ?? 0}} }">
                            <a href="#!"
                               class="inline-flex items-center justify-center rounded-md transition w-full py-1 px-2"
                               :class="eventsCount <= 0
                                        ? 'bg-gray-300 text-gray-400 cursor-not-allowed pointer-events-none'
                                        : 'bg-transparent hover:bg-blue-600 hover:font-bold group border border-gray-500'"
                               :tabindex="eventsCount <= 0 ? -1 : 0"
                               :aria-disabled="eventsCount <= 0"
                               @click="if(eventsCount > 0) sendEvent({{$instance->id}})"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                     stroke-width="1.5" stroke="currentColor"
                                     class="size-6 transition-colors group-hover:stroke-white group-hover:stroke-2">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M21 7.5V18M15 7.5V18M3 16.811V8.69c0-.864.933-1.406 1.683-.977l7.108 4.061a1.125 1.125 0 0 1 0 1.954l-7.108 4.061A1.125 1.125 0 0 1 3 16.811Z"/>
                                </svg>
                            </a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <hr class="my-4">
    </div>
@endsection

@push('scripts')
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

            // Trigger the animation
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

       function sendEvent(instanceId) {
            // Swal.fire({
            //     title: 'Processing...',
            //     text: 'Please wait while the event is being sent.',
            //     allowOutsideClick: false,
            //     allowEscapeKey: false,
            //     didOpen: () => {
            //         Swal.showLoading();
            //     }
            // });

            fetch("{{route('cvedixrt_event.broadcast')}}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({instance_id: instanceId, _action: 'sendSingleEvent'})
            }).then(response => {
                if (response.ok) {
                    return response.json();
                } else {
                    throw new Error('Network response was not ok');
                }
            }).then(data => {
                Swal.close();
                if (data.success === 'ok') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: data.message,
                        showConfirmButton: false,
                        timer: 750
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'An error occurred while sending events.',
                        confirmButtonText: 'Close',
                        confirmButtonColor: '#dc3545',
                    });
                }
            }).catch(error => {
                Swal.close();
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'An error occurred while sending events.',
                    confirmButtonText: 'Close',
                    confirmButtonColor: '#dc3545',
                });
            });
        }
    </script>
@endpush
