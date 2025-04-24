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
                        @if ($row->deleted_at !== null)

                            <!-- <form action="{{ route('user.enterprise.eservice.restore', $row['id']) }}" method="POST"
                                                                                    style="display:inline;"
                                                                                    onsubmit="return confirm('{{ __('eservice-index.restore_confirm') }}');">

                                                                                    <button type="submit" class="btn btn-success ">{{ __('eservice-index.restore') }}</button>
                                                                                </form> -->

                            <a href="javascript:;" data-dismiss="modal" data-toggle="modal" data-target="#restore-modal"
                                class="btn btn-outline-danger mr-5">
                                {{ __('eservice-index.restore') }}
                            </a>

                        @endif

                        @if ($can_be_deleted)
                            <a href="javascript:;" data-dismiss="modal" data-toggle="modal" data-target="#delete-modal"
                                class="btn btn-outline-danger mr-5">
                                {{ $row->deleted_at !== null ? __('eservice-update.force-delete-button') : __('eservice-update.soft-delete-button') }}
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
                'route' => route('user.enterprise.eservice.delete', $row->id),
            ])

                                @includeWhen(true, 'molecules.restore-modal', [
                                    'title' => __('eservice-update.restore-title'),
                                    'message' => __('eservice-update.restore-message'),
                                    'route' => route('user.enterprise.eservice.restore', $row->id),
                                ])
                                                                                                                                                                                                                                                                                                                                                                                                    </div>
                                                                                                                                                                                                                                                                                                                                                                                        </div>

@endsection
