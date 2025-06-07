@extends('layouts.in')

@section('body')
    <form method="post">
        <input type="hidden" name="_action" value="create" />

        @include('domains.cvedixrt.solution.molecules.create-update')

        <div class="box p-5 mt-5">
            <div class="text-right">
                <button type="submit" class="btn btn-primary">{{ __('cvedixrt-solution-create.btn-save') }}</button>
                <a class="btn btn-secondary ml-2" href="{{ route('cvedixrt_solution.index') }}">{{ __('cvedixrt-solution-create.btn-cancel') }}</a>
            </div>
        </div>
    </form>
@endsection
