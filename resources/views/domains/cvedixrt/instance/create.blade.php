@extends('layouts.in')

@section('body')
    <form method="post">
        <input type="hidden" name="_action" value="next"/>
        <div class="box mb-5 p-5">
            @include('domains.cvedixrt.instance.molecules.create-update')

            <div class="box p-5 mt-5">
                <div class="text-right">
                    <button type="submit" class="btn btn-primary">{{ __('cvedixrt-instance-create.btn-next') }}</button>
                    <a class="btn btn-secondary ml-2"
                       href="{{ route('cvedixrt_instance.index') }}">{{ __('cvedixrt-instance-create.btn-cancel') }}</a>
                </div>
            </div>
        </div>
    </form>
@endsection
