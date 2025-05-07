@extends ('domains.vehicle.index-layout')

@section('content')
    <form method="post">
        <input type="hidden" name="_action" value="create" />

        @include('domains.vehicle-group.molecules.create-update')

        <div class="box p-5 mt-5">
            <div class="text-right">
                <button type="submit" class="btn btn-primary">{{ __('vehicle-group-create.btn-save') }}</button>
                <a class="btn btn-secondary ml-2" href="{{ route('vehicle_group.index') }}">{{ __('vehicle-group-create.btn-cancel') }}</a>
            </div>
        </div>
    </form>
@endsection
