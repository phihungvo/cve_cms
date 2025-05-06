@extends('layouts.in')

@section('title', __('notification-update.title'))

@section('body')
    <div class="intro-y box p-5">
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4 p-4">{{ session('error') }}</div>
        @endif

        <h2 class="text-lg font-medium mb-4">{{ __('notification-update.title') }}</h2>

        <form method="POST" action="{{ route('notification.update', $notification->id) }}" class="form">
            @csrf
            @method('PATCH')

            <!-- Tiêu đề -->
            <div class="mb-4">
                <label for="title" class="form-label">{{ __('notification-update.title-label') }}</label>
                <input type="text" name="title" id="title" class="form-control form-control-lg" required
                    placeholder="{{ __('notification-update.title-placeholder') }}"
                    value="{{ old('title', $notification->title) }}">
         
            </div>

            <!-- Nội dung -->
            <div class="mb-4">
                <label for="content" class="form-label">{{ __('notification-update.content-label') }}</label>
                <textarea name="content" id="content" class="form-control form-control-lg" required
                    placeholder="{{ __('notification-update.content-placeholder') }}">{{ old('content', $notification->content) }}</textarea>
             
            </div>

            <!-- Loại thông báo -->
            <div class="mb-4">
                <label for="notification_type" class="form-label">{{ __('notification-update.type-label') }}</label>
                <select name="notification_type" id="notification_type" class="form-control form-control-lg" required>
                    @if($is_root)
                        <option value="system" {{ old('notification_type', $notification->notification_type) === 'system' ? 'selected' : '' }}>
                            {{ __('notification-update.type-system') }}
                        </option>
                    @endif
                    <option value="enterprise" {{ old('notification_type', $notification->notification_type) === 'enterprise' ? 'selected' : '' }}>
                        {{ __('notification-update.type-enterprise') }}
                    </option>
                </select>
           
            </div>

            <!-- Nhóm mục tiêu -->
            <div class="mb-4">
                <label for="target_group" class="form-label">{{ __('notification-update.target-group-label') }}</label>
                <select name="target_group" id="target_group" class="form-control form-control-lg">
                    <option value="">{{ __('notification-update.target-group-all') }}</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}" {{ old('target_group', $notification->target_group) === $role ? 'selected' : '' }}>
                            {{ $role }}
                        </option>
                    @endforeach
                </select>
               
            </div>

            <!-- Nút submit -->
            <div class="mt-6">
                <button type="submit" class="btn btn-primary">{{ __('notification-update.submit') }}</button>
                <a href="{{ route('notification.index') }}" class="btn btn-secondary ml-2">
                    {{ __('notification-update.cancel') }}
                </a>
            </div>
        </form>
    </div>
@endsection