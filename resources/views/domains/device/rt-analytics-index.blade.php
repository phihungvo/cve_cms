@extends('domains.device.update-layout')

@section('content')
    <div class="intro-y box p-5 mt-5">
        <!-- Display Success or Error Messages -->
        <h2 class="text-lg font-medium mb-5">{{ __('rt-analytics-index.all-instance-list') }}</h2>
    @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger mb-4">
                {{ session('error') }}
            </div>
        @endif
        @if($row->enable_ai && $row->enabled)
            <!--  -->
            <form method="get">
                <div class="sm:flex sm:space-x-4  justify-between items-center py-2">
                    <!--Input search -->
                    <div class="flex-grow mt-2 sm:mt-0">
                        <input type="search" name="" id=""
                               class="form-control form-select-lg px-2"
                               placeholder="{{__('rt-analytics-index.filter')}}"
                               data-table-search="#device-cvedix-instance-list-table">
                    </div>
                    <!--Select Filter Solution-->
                    <div class="sm:ml-4 mt-2 sm:mt-0">
                        <x-select name="solution_id"
                                  :options="$solutions"
                                  value="id" text="solution_name"
                                  placeholder="{{__('rt-analytics-index.filter-solutions')}}"
                                  data-change-submit></x-select>
                    </div>
                    <!--Select Filter Group-->
                    <div class="sm:ml-4 mt-2 sm:mt-0">
                        <x-select name="group_id"
                                  :options="$groups"
                                  value="id" text="group_name"
                                  placeholder="{{__('rt-analytics-index.filter-groups')}}"
                                  data-change-submit></x-select>
                    </div>

                    <!--Btn create instance-->
                    <div class="sm:ml-4 mt-2 sm:mt-0 bg-white">
                        <a class="btn form-control-lg whitespace-nowrap"
                           href="{{route('device.runtime-analytics.create', $row->id)}}">
                            {{__('rt-analytics-index.create-instance')}}
                        </a>
                    </div>
                </div>
            </form>

            {{--            table show list instance--}}
            <div class="overflow-auto scroll-visible header-sticky">
                <table id="device-cvedix-instance-list-table"
                       class="table table-report sm:mt-2 font-medium font-semibold text-center whitespace-nowrap"
                       data-table-sort
                       data-table-pagination data-table-pagination-limit="10">
                    <thead>
                    <tr>
                        <th class="text-center">{{ __('rt-analytics-index.no') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.instance-id') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.name') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.source') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.zones') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.lines') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.solutions') }}</th>
                        <th class="text-center">{{ __('rt-analytics-index.groups') }}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($list as $index => $instance)
                        @php
                            $link = route('device.runtime-analytics.update', $row->id);
//                            $updateAt = isset($instance->updated_at)
//                        ? \Carbon\Carbon::parse($instance->updated_at)->setTimezone('Asia/Ho_Chi_Minh')
//                        : '-'
                        @endphp
                        <tr>
                            <!-- No -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}" class="block">{{$index + 1}}</a></td>
                            <!-- ID -->
                            <td>
{{--                                <a href="{{$link}}?instanceId={{$instance->id}}" class="block">{{$instance->id}}</a>--}}
                                <a href="javascript:void(0);" onclick="copyToClipboard(this);" class="ml-2" data-uuid="{{$instance->uuid}}"
                                   style="display: inline-block;height: 16px; width: 16px;">
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 115.77 122.88" class="icon-svg">
                                        <style>.st0 { fill-rule: evenodd; clip-rule: evenodd; }</style>
                                        <g>
                                            <path class="st0"
                                                  d="M89.62,13.96v7.73h12.19h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02v0.02v73.27v0.01h-0.02c-0.01,3.84-1.57,7.33-4.1,9.86c-2.51,2.5-5.98,4.06-9.82,4.07v0.02h-0.02h-61.7H40.1v-0.02c-3.84-0.01-7.34-1.57-9.86-4.1c-2.5-2.51-4.06-5.98-4.07-9.82h-0.02v-0.02V92.51H13.96h-0.01v-0.02c-3.84-0.01-7.34-1.57-9.86-4.1c-2.5-2.51-4.06-5.98-4.07-9.82H0v-0.02V13.96v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07V0h0.02h61.7h0.01v0.02c3.85,0.01,7.34,1.57,9.86,4.1c2.5,2.51,4.06,5.98,4.07,9.82h0.02V13.96L89.62,13.96z M79.04,21.69v-7.73v-0.02h0.02c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v64.59v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h12.19V35.65v-0.01h0.02c0.01-3.85,1.58-7.34,4.1-9.86c2.51-2.5,5.98-4.06,9.82-4.07v-0.02h0.02H79.04L79.04,21.69z M105.18,108.92V35.65v-0.02h0.02c0-0.91-0.39-1.75-1.01-2.37c-0.61-0.61-1.46-1-2.37-1v0.02h-0.01h-61.7h-0.02v-0.02c-0.91,0-1.75,0.39-2.37,1.01c-0.61,0.61-1,1.46-1,2.37h0.02v0.01v73.27v0.02h-0.02c0,0.91,0.39,1.75,1.01,2.37c0.61,0.61,1.46,1,2.37,1v-0.02h0.01h61.7h0.02v0.02c0.91,0,1.75-0.39,2.37-1.01c0.61-0.61,1-1.46,1-2.37h-0.02V108.92L105.18,108.92z"/>
                                        </g>
                                    </svg>
                                </a>


                            </td>
                            <!-- Name -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}"
                                   class="block">{{$instance->instance_name}}</a></td>
                            <!-- Source -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}"
                                   class="block">{{$instance->input_source ?? '-'}}</a></td>
                            <!-- Zones -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}"
                                   class="block">{{$instance->instance_zones ?? '-'}}</a></td>
                            <!-- Lines -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}"
                                   class="block">{{$instance->instance_lines ?? '-'}}</a></td>
                            <!-- Solutions -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}"
                                   class="block">{{$instance->solution->solution_name ?? '-'}}</a></td>
                            <!-- Groups -->
                            <td><a href="{{$link}}?instanceId={{$instance->id}}"
                                   class="block">{{$instance->group->group_name ?? '-'}}</a></td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <hr class="my-4">
        @else
            <div class="mb-4">
                <p class="text-sm text-gray-500">{{ __('rt-analytics-index.rt-analytics-not_supported') }}</p>
            </div>
        @endif
    </div>
@stop

@push('scripts')
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
    </script>
@endpush
