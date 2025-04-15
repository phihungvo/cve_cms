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

        <form method="POST" action="{{ route('fpp.media.create') }}" enctype="multipart/form-data">
            @csrf

            <!-- Name -->
            <div class="mb-4">
                <label class="form-label">{{ __('Media Name') }}</label>
                <input type="text" name="name" class="form-control" required value="{{ old('name') }}">
            </div>

            <!-- Media Files -->
            <div class="mb-4">
                <label class="form-label">{{ __('Media Files') }}</label>
                <input type="file" name="media_files[]" class="form-control" accept="video/mp4" multiple required>
                <small class="text-muted">{{ __('Multiple MP4 files allowed, max 10 files, total 1GB') }}</small>
            </div>

            <!-- Campaign -->
            <div class="mb-4">
                <label class="form-label">{{ __('Campaign') }}</label>
                <select name="campaign_id" class="form-select">
                    <option value="">{{ __('Select Campaign') }}</option>
                    @foreach($campaignOptions as $id => $name)
                        <option value="{{ $id }}" {{ old('campaign_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Enterprise -->
            <div class="mb-4">
                <label class="form-label">{{ __('Enterprise') }}</label>
                <select name="enterprise_id" class="form-select" required>
                    <option value="">{{ __('Select Enterprise') }}</option>
                    @foreach($enterpriseOptions as $id => $name)
                        <option value="{{ $id }}" {{ old('enterprise_id') == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-5">
                <button type="submit" class="btn btn-primary">
                    {{ __('Upload Media') }}
                </button>
                <a href="{{ route('fpp.media.index') }}" class="btn btn-secondary ml-2">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection