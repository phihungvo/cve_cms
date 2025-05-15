@extends ('domains.schedule.index-layout')

@section('content')
    <form method="post">
        <input type="hidden" name="_action" value="create" />

        @include('domains.schedule-group.molecules.create-update')

        <div class="box p-5 mt-5">
            <div class="text-right">
                <button type="submit" class="btn btn-primary">{{ __('schedule-group-create.btn-save') }}</button>
                <a class="btn btn-secondary ml-2" href="{{ route('schedule_group.index') }}">{{ __('schedule-group-create.btn-cancel') }}</a>
            </div>
        </div>
    </form>
@endsection
