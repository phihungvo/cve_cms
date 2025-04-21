@extends('domains.device.update-layout')

@section('content')
    <div class="intro-y box p-5 mt-5">
        <h2 class="text-lg font-medium mb-5">{{ __('camera.runtime-analytics') }}</h2>

        <!-- Display Success or Error Messages -->
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

        @if($row->enable_ai)
            <!--  -->
            <h2>Runtime analytics available</h2>
            <div class="box mb-5 p-5">
                <button class="flex align-center text-base font-medium py-2 cursor-pointer w-full rounded-md"
                        onclick="toggleForm('create-instance-form')">
                    <span class="flex-1 text-left">{{ __('cvedit-instance.create') }}</span>

                    <svg class="w-5 h-5 transform transition-transform duration-200" id="path-icon" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div id="create-instance-form">
                    <form action="" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:gap-4 sm:gap-0">
                            <!-- Name -->
                            <div>
                                <label class="form-label">{{__('cvedit-instance.name')}}</label>
                                <span class="text-red-500">*</span>
                                <input type="text" name="instance_name" class="form-control" required
                                       value="{{old(('instance_name'))}}">
                            </div>
                            <!-- UUID -->
                            <div>
                                <label class="form-label">UUID</label>
                                <span class="text-red-500">*</span>
                                <div class="input-group">
                                    <input type="text" name="instance_uuid" class="form-control" required readonly
                                           value="{{old(('instance_uuid'))}}" id="instance_uuid">
                                    <button type="button" class="input-group-text input-group-text-lg"
                                            title="{{__('common.generate')}}"
                                            data-password-generate="#instance_uuid" data-password-generate-format="uuid"
                                            tabindex="-1">@icon('refresh-cw', 'w-5 h-5')
                                    </button>

                                </div>
                            </div>
<!-- instance source -->
                            <div>
                                <label class="form-label">{{__('cvedit-instance.source')}}</label>
                                <span class="text-red-500">*</span>
                                <input type="text" name="instance_name" class="form-control" required
                                       value="{{old(('instance_source'))}}">
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <hr class="my-4">
        @else
            <div class="mb-4">
                <p class="text-sm text-gray-500">{{ __('camera.rt-analytics-not_supported') }}</p>
            </div>
        @endif
    </div>
@stop
