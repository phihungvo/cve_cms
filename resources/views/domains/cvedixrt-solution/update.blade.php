@extends('layouts.in')

@section('body')
    <form method="POST">
        <input type="hidden" name="_action" value="update" />

        @include('domains.cvedixrt-solution.molecules.create-update')

        @php
            $isDeleted = is_null($row->deleted_at);
        @endphp

        <div class="box p-5 mt-5">
            <div class="flex justify-end items-center">
                @if($isDeleted)
                    <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                       class="btn btn-outline-danger mr-2">{{ __('Delete') }}</a>
                @else
                    <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                       class="btn btn-danger mr-2">{{ __('Force Delete') }}</a>
                    <a href="javascript:;" data-toggle="modal" data-target="#restore-modal"
                       class="btn btn-outline-success mr-2">{{ __('Restore') }}</a>
                @endif

                @if($isDeleted)
                    <button type="submit" class="btn btn-primary">{{ __('Save') }}</button>
                @endif
                <a class="btn btn-secondary ml-2" href="{{ route('cvedixrt_solution.index') }}">{{ __('Cancel') }}</a>
            </div>
        </div>
    </form>

    @include('molecules.delete-modal', [
        'title' => $isDeleted ? __('Delete') : __('Force Delete'),
        'message' => $isDeleted ? __('Are you sure you want to delete this item?') : __('Are you sure you want to force delete this item?'),
        'action' => $isDeleted ? 'delete' : 'forceDelete'
    ])

    @include('molecules.restore-modal', [
        'title' => __('Restore CvedixrtSolution'),
        'message' => __('Are you sure you want to restore this item?'),
        'action' => 'restore'
    ])
@endsection
