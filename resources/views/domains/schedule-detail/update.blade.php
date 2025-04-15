@extends('layouts.in')

@section('title', __('schedule-update.title'))

@section('body')
    <div class="intro-y box p-5">
        <h2 class="text-lg font-medium mb-5">{{ __('Update Schedule Detail') }}</h2>

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

        <form method="POST" action="{{ route('schedule-detail.update', $scheduleDetail->id) }}">
            @csrf
            @method('PUT')

            <!-- Schedule -->
            <div class="mb-4">
                <label class="form-label">{{ __('Schedule') }}</label>
                <select name="schedule_id" class="form-select" required>
                    @foreach($scheduleOptions as $id => $name)
                        <option value="{{ $id }}" {{ old('schedule_id', $scheduleDetail->schedule_id) == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Playlist -->
            <div class="mb-4">
                <label class="form-label">{{ __('Playlist') }}</label>
                <select name="playlist_id" class="form-select" required>
                    @foreach($playlistOptions as $id => $name)
                        <option value="{{ $id }}" {{ old('playlist_id', $scheduleDetail->playlist_id) == $id ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Start Time (DateTime) -->
            <div class="mb-4">
                <label class="form-label">{{ __('Start Time') }}</label>
                <input type="datetime-local" name="start_time" class="form-control" required
                    value="{{ old('start_time', $scheduleDetail->start_time ? $scheduleDetail->start_time->format('Y-m-d\TH:i') : '') }}">
            </div>

            <!-- End Time (DateTime) -->
            <div class="mb-4">
                <label class="form-label">{{ __('End Time') }}</label>
                <input type="datetime-local" name="end_time" class="form-control"
                    value="{{ old('end_time', $scheduleDetail->end_time ? $scheduleDetail->end_time->format('Y-m-d\TH:i') : '') }}">
            </div>

            <!-- Repeat -->
            <div class="p-2">
                <div class="form-check">
                    <input type="checkbox" name="repeat" value="1" class="form-check-switch"
                        id="repeat-{{ $scheduleDetail->id }}" {{ old('repeat', $scheduleDetail->repeat) ? 'checked' : '' }}>
                    <label for="repeat-{{ $scheduleDetail->id }}" class="form-check-label">{{ __('Repeat') }}</label>
                </div>
            </div>

            <div class="mt-5">
                <button type="submit" class="btn btn-primary">
                    {{ __('Update Schedule Detail') }}
                </button>
                <a href="{{ route('schedule-detail.index') }}" class="btn btn-secondary ml-2">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection