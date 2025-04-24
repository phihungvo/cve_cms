@extends('layouts.in')

@section('title', __('media-create.title'))

@section('body')
    <div class="intro-y box p-5">
        <h2 class="text-lg font-medium mb-5">{{ __('Create New Media') }}</h2>

        @if (session('success'))
            <div class="alert alert-success mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger mb-4">
                {{ session('error') }}
            </div>
        @endif

        <livewire:media-upload />

        <div class="mt-5">
            <show-canvas id="media-create-canvas" />
            <a href="{{ route('fpp.media.index') }}" class="btn btn-secondary">
                {{ __('Cancel') }}
            </a>
        </div>
    </div>
@endsection