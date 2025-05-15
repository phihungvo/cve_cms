@extends('layouts.in')

@section('body')
    <form method="post">
        <input type="hidden" name="_action" value="create" />

        @include('domains.cvedix-instance.molecules.create-update')

        <div class="box p-5 mt-5">
            <div class="text-right">
                <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                <a class="btn btn-secondary ml-2" href="{{ route('cvedix_instance.index') }}">{{ __('Cancel') }}</a>
            </div>
        </div>
    </form>
@endsection
