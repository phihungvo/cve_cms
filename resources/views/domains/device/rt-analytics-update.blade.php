@extends('domains.device.update-layout')

@section('content')
    <div class="intro-y box p-5 mt-5">
        <h2 class="text-lg font-medium mb-5">{{ __('rt-analytics-create.edit-instance') }}</h2>

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
        @if($row->enable_ai && $row->enabled)
            <!--  -->
            <div class="box mb-5 p-5">
                <div id="update-instance-form">
                    <form method="POST">
                        <input type="hidden" name="_action" value="update"/>
                        @csrf

                        {{--include phan chung cua 2 form create update--}}
                        @include('domains.device.molecules.create-update-instance')

                        <div class="box  p-5 mt-5">
                            <div class="text-right">
                                <!--Btn Delete -->
                                <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                   class="btn btn-danger mr-2"
                                >{{__('Delete')}}</a>
                                <!--Btn save -->
                                <button type="submit" class="btn btn-primary">save</button>
                                <!--Btn cancel-->
                                <a href="{{ route('device.runtime-analytics', ['id' => $row->id]) }}"
                                   class="btn btn-outline-secondary ml-2">{{__('rt-analytics-create.btn-cancel')}}</a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <hr class="my-4">
        @else
            <div class="mb-4">
                <p class="text-sm text-gray-500">{{ __('rt-analytics-create.rt-analytics-not_supported') }}</p>
            </div>
        @endif
    </div>

    @include ('molecules.delete-modal', [
      'title' => __('Delete Instance'),
      'message' => __('Are you sure delete Instance?'),
    ])

@stop
