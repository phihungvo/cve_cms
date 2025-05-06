@extends('domains.device.index-layout')

@section('content')
    <form method="post">
        <input type="hidden" name="_action" value="create" />

        @include('domains.device-group.molecules.create-update')

        <div class="box p-5 mt-5">
            <div class="text-right">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a class="btn btn-secondary ml-2" href="{{ route('device_group.index') }}">{{ __('Cancel') }}</a>
            </div>
        </div>
    </form>
@endsection
