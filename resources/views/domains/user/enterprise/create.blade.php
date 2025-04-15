@extends('layouts.in')

@section('body')
    <div>
        <div class="tab-content">
            <div class="tab-pane active" role="tabpanel">
                <form method="post" action="{{route('user.enterprise.store')}}">
                    @csrf
                    <!-- <input type="hidden" name="_action" value="create" /> -->

                    @includeif('domains.user.enterprise.molecules.create-update')

                    <div class="box p-5 mt-5">
                        <div class="text-right">
                            <button type="submit" class="btn btn-primary">{{__('enterprise-create.save')}}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
