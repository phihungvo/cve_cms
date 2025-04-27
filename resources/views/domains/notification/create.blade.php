@extends('layouts.in')

@section('title', __('notification-create.title'))

@section('body')
    <div class="intro-y box p-5">
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4 p-4">{{ session('error') }}</div>
        @endif

        <h2 class="text-lg font-medium mb-4">{{ __('notification-create.title') }}</h2>

        <form method="POST" action="{{ route('notification.create') }}" class="form">
            @csrf

            <!-- Tiêu đề -->
            <div class="mb-4">
                <label for="title" class="form-label">{{ __('notification-create.title-label') }}</label>
                <input type="text" name="title" id="title" class="form-control form-control-lg" required
                    placeholder="{{ __('notification-create.title-placeholder') }}" value="{{ old('title') }}">
                @error('title')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Nội dung -->
            <div class="mb-4">
                <label for="content" class="form-label">{{ __('notification-create.content-label') }}</label>
                <textarea name="content" id="content" class="form-control form-control-lg" required
                    placeholder="{{ __('notification-create.content-placeholder') }}">{{ old('content') }}</textarea>
                @error('content')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Loại thông báo -->
            <div class="mb-4">
                <label for="notification_type" class="form-label">{{ __('notification-create.type-label') }}</label>
                <select name="notification_type" id="notification_type" class="form-control form-control-lg" required>
                    @if($is_root)
                        <option value="system" {{ old('notification_type') === 'system' ? 'selected' : '' }}>
                            {{ __('notification-create.type-system') }}
                        </option>
                    @endif
                    <option value="enterprise" {{ old('notification_type') === 'enterprise' ? 'selected' : '' }}>
                        {{ __('notification-create.type-enterprise') }}
                    </option>
                </select>
                @error('notification_type')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Nhóm mục tiêu -->
            <div class="mb-4">
                <label for="target_group" class="form-label">{{ __('notification-create.target-group-label') }}</label>
                <select name="target_group" id="target_group" class="form-control form-control-lg">
                    <option value="">{{ __('notification-create.target-group-all') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ old('target_group') === $role ? 'selected' : '' }}>
                            {{ $role }}
                        </option>
                    @endforeach
                </select>
                @error('target_group')
                    <div class="text-danger mt-2">{{ $message }}</div>
                @enderror
            </div>

            <!-- Nút submit -->
            <div class="mt-6">
                <button type="submit" class="btn btn-primary">{{ __('notification-create.submit') }}</button>
                <a href="{{ route('notification.index') }}" class="btn btn-secondary ml-2">
                    {{ __('notification-create.cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection