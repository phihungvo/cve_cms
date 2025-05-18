@extends('layouts.in')

@section('body')

    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            <form method="post" action="{{ route('user.enterprise.billing.update', $row->id) }}">
                @csrf
                @method('PATCH')
                <input type="hidden" name="_action" value="update" />

                @include('domains.user.enterprise.billing.molecules.create-update')

                <div class="box p-5 mt-5">
                    <div class="flex justify-between">
                        <div>
                            <button type="button" onclick="window.history.back()" class="btn btn-outline-danger">
                                {{ __('billing.back') }}
                            </button>

                        </div>

                        <div>
                            @if ($row->deleted_at !== null)
                                <a href="javascript:;" data-dismiss="modal" data-toggle="modal" data-target="#restore-modal"
                                    class="btn btn-success mr-5">
                                    {{ __('billing.restore') }}
                                </a>

                            @endif

                            @if ($can_be_deleted)
                                <a href="javascript:;" data-dismiss="modal" data-toggle="modal" data-target="#delete-modal"
                                    class="btn btn-outline-danger mr-5">
                                    {{ $row->deleted_at !== null ? __('billing.force-delete-button') : __('billing.soft-delete-button') }}
                                </a>
                            @endif

                            <button type="submit" class="btn btn-primary" data-click-one>
                                {{ __('billing.update') }}
                            </button>
                        </div>
                    </div>
                </div>

            </form>

            @includeWhen($can_be_deleted, 'molecules.delete-modal', [
                'title' => __('billing.update.delete-title'),
                'message' => __('billing.update.delete-message'),
                'route' => route('user.enterprise.license.delete', $row->id),
            ])

            @includeWhen(true, 'molecules.restore-modal', [
                'title' => __('billing.update.restore-title'),
                'message' => __('billing.update.restore-message'),
                'route' => route('user.enterprise.license.restore', $row->id),
            ])
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            </div>
                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                </div>

@endsection
