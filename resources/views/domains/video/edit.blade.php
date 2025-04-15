@extends('layouts.in')

@section('body')
    <h1>{{ __('video-edit.title') }}</h1>

    <form method="POST" action="{{ route('video.update', $video->id) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label>{{ __('video-index.name') }}</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $video->name) }}" required>
        </div>
        <div class="form-group">
            <label>{{ __('video-index.video_url') }}</label>
            <input type="url" name="video_url" class="form-control" value="{{ old('video_url', $video->video_url) }}"
                required>
        </div>
        <div class="form-group">
            <label>{{ __('video-index.reach_target') }}</label>
            <input type="number" name="reach_target" class="form-control"
                value="{{ old('reach_target', $video->reach_target) }}" step="1" min="0" required>
        </div>
        <div class="form-group">
            <label>{{ __('video-index.distance_target') }}</label>
            <input type="number" name="distance_target" class="form-control"
                value="{{ old('distance_target', $video->distance_target) }}" step="1" min="0" required>
        </div>
        <div class="form-group">
            <label>{{ __('video-index.impression_target') }}</label>
            <input type="number" name="impression_target" class="form-control"
                value="{{ old('impression_target', $video->impression_target) }}" step="1" min="0" required>
        </div>
        <div class="form-group">
            <label>{{ __('video-index.device_target') }}</label>
            <input type="number" name="device_target" class="form-control"
                value="{{ old('device_target', $video->device_target) }}" step="1" min="0" required>
        </div>
        <div class="form-group">
            <label>{{ __('video-index.cpm_target') }}</label>
            <input type="number" name="cpm_target" class="form-control"
                value="{{ old('cpm_target', $video->cpm_target) }}" step="0.01" min="0" required>
        </div>
        <div class="form-group">
            <label>{{ __('video-index.cost') }}</label>
            <input type="number" name="cost" class="form-control" value="{{ old('cost', $video->cost) }}"
                step="0.01" min="0" required>
        </div>
        <div class="form-group">
            <label>{{ __('video-index.video_type') }}</label>
            <select name="video_type" class="form-control" required>
                <option value="1" {{ old('video_type', $video->video_type) == '1' ? 'selected' : '' }}>AdBike</option>
                <option value="2" {{ old('video_type', $video->video_type) == '2' ? 'selected' : '' }}>AdCar</option>
                <option value="0" {{ old('video_type', $video->video_type) == '0' ? 'selected' : '' }}>AdOther
                </option>
            </select>
        </div>
        {{-- <div class="form-group">
            <label>{{ __('video-index.enabled') }}</label>
            <select name="enabled" class="form-control" required>
                <option value="1" {{ old('enabled', $video->enabled) == '1' ? 'selected' : '' }}>Playing</option>
                <option value="0" {{ old('enabled', $video->enabled) == '0' ? 'selected' : '' }}>Stop</option>
            </select>
        </div> --}}
        <button type="submit" class="btn btn-primary">{{ __('video-edit.save') }}</button>
    </form>
@stop
