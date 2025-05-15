
@extends('layouts.in')
@section('body')
    <form method="post">
        <input type="hidden" name="_action" value="create" />
{{--        <input type="hidden" name="">--}}

        @include('domains.campaign.playlist.molecules.create-update')

        <div class="box p-5 mt-5">
            <!-- Show Devices -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($devices as $device)
                    <div class="card mb-3 device bg-white" id="device-{{ $device['id'] }}" device-id="{{$device['id']}}" onmouseover="this.style.backgroundColor='#f0f0f0';" onmouseout="this.style.backgroundColor='white';">
                        <div class="shadow-md rounded-lg overflow-hidden">
                            <div class="p-4">
                                <div class="flex justify-between items-center">
                                    <h5 class="text-lg font-bold">{{ $device['name'] }}</h5>
                                    <input type="checkbox" name="deviceIds[]" value="{{ $device['id'] }}" class="form-check-switch" id="device-{{ $device['id'] }}">
                                </div>
                                <p class="text-gray-500">{{ $device['model'] }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="box p-5 mt-5">
            <div class="text-right">
                <button type="submit" class="btn btn-primary">{{__('playlist-create.save')}}</button>
                <a href="{{route('fpp.playlist.index')}}" class="btn btn-secondary ml-2">{{__('playlist-create.cancel')}}</a>
            </div>
        </div>

    </form>
@endsection
