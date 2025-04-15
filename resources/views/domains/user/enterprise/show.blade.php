@extends('layouts.in')

@section('body')
    <div class="table-content">
        <div class="tab-pane active" role="tabpanel">
            @php $errors = session('errors') @endphp
            @if ($errors && $errors->any())
                <div class="alert alert-danger" style="display: block">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form method="post" action="{{route('user.enterprise.update', $enterprises['id'])}}">
                @csrf
                @method('put')
                <!-- <input type="hidden" name="_action" value="create" /> -->

                @includeif('domains.user.enterprise.molecules.update')

                <div class="box p-5 mt-5">
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">{{__('enterprise-create.save')}}</button>
                    </div>
                </div>
            </form>
        </div>

    </div>
@stop
