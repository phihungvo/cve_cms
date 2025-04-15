@extends('layouts.in')

@section('body')

    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            <form method="post" action="{{ route('user.role.feature.update', $row->id) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="_action" value="update" />

                @include('domains.user.role.feature.molecules.create-update')

                <div class="box p-5 mt-5">
                    <div class="text-right">
                        @if ($can_be_deleted)
                            <a href="javascript:;" data-dismiss="modal" data-toggle="modal" data-target="#delete-modal"
                                class="btn btn-outline-danger mr-5">
                                {{ __('role-feature-update.delete-button') }}
                            </a>
                        @endif

                        <button type="submit" class="btn btn-primary" data-click-one>
                            {{ __('role-feature-update.update') }}
                        </button>
                    </div>
                </div>
            </form>

            @includeWhen($can_be_deleted, 'molecules.delete-modal', [
                'title' => __('role-feature-update.delete-title'),
                'message' => __('role-feature-update.delete-message'),
                'route' => route('user.role.feature.delete', $row->id),
            ])
        </div>
    </div>

@endsection