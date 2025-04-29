@extends('layouts.in')

@section('body')
    <form method="post">
        <input type="hidden" name="_action" value="create" />

        @include('domains.user-group.molecules.create-update')

        <div class="box p-5 mt-5">
            <div class="text-right">
                <!--btn save -->
                <button type="submit" class="btn btn-primary">{{__('Save')}}</button>
                <!--btn cancel -->
                <a class="btn btn-secondary ml-2" href="{{route('group.index')}}">{{__('Cancel')}}</a>
            </div>
        </div>

@endsection
