@extends ('domains.schedule.index-layout')

@section('content')
    <form method="POST">
        <input type="hidden" name="_action" value="update" />

        @include('domains.schedule-group.molecules.create-update')

        @php
            $isDeleted = is_null($row->deleted_at);
        @endphp

        <div class="box p-5 mt-5">
            <div class="flex justify-end items-center">
                @if($isDeleted)
                    <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                       class="btn btn-outline-danger mr-2">{{ __('schedule-group-update.btn-delete') }}</a>
                @else
                    <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                       class="btn btn-danger mr-2">{{ __('schedule-group-update.btn-force-delete') }}</a>
                    <a href="javascript:;" data-toggle="modal" data-target="#restore-modal"
                       class="btn btn-outline-success mr-2">{{ __('schedule-group-update.btn-restore') }}</a>
                @endif

                @if($isDeleted)
                    <button type="submit" class="btn btn-primary">{{ __('schedule-group-update.btn-save') }}</button>
                @endif
                <a class="btn btn-secondary ml-2" href="{{ route('schedule_group.index') }}">{{ __('schedule-group-update.btn-cancel') }}</a>
            </div>
        </div>
    </form>

    @include('molecules.delete-modal', [
        'title' => $isDeleted ? __('schedule-group-update.modal.delete.title') : __('schedule-group-update.modal.force-delete.title'),
        'message' => $isDeleted ? __('schedule-group-update.modal.delete.message') : __('schedule-group-update.modal.force-delete.message'),
        'action' => $isDeleted ? 'delete' : 'forceDelete'
    ])

    @include('molecules.restore-modal', [
        'title' => __('schedule-group-update.modal.restore.title'),
        'message' => __('schedule-group-update.modal.restore.message'),
        'action' => 'restore'
    ])
@endsection
