@extends('layouts.in')

@section('body')

    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            <form method="post" action="{{ route('user.enterprise.eservice.update', $row->id) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="_action" value="update" />

                @include('domains.user.enterprise.eservice.molecules.create-update')

                <div class="box p-5 mt-5">
                    <div class="text-right">
                        @if ($can_be_deleted)
                            <a href="javascript:;" data-dismiss="modal" data-toggle="modal" data-target="#delete-modal"
                                class="btn btn-outline-danger mr-5">
                                {{ __('eservice-update.delete-button') }}
                            </a>
                        @endif

                        <button type="submit" class="btn btn-primary" data-click-one>
                            {{ __('eservice-update.update') }}
                        </button>
                    </div>
                </div>
            </form>

            @includeWhen($can_be_deleted, 'molecules.delete-modal', [
                'title' => __('eservice-update.delete-title'),
                'message' => __('eservice-update.delete-message'),
                // 'route' => route('user.permission.delete', $row->id),
                'method' => 'delete',
            ])
            </div>
        </div>

@endsection
