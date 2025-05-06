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

            </div>

            <!-- Nội dung -->
            <div class="mb-4">
                <label for="content" class="form-label">{{ __('notification-create.content-label') }}</label>
                <textarea name="content" id="content" class="form-control form-control-lg" required
                    placeholder="{{ __('notification-create.content-placeholder') }}">{{ old('content') }}</textarea>

            </div>

            <!-- Loại thông báo -->
            <div class="mb-4">
                <label for="notification_type" class="form-label">{{ __('notification-create.type-label') }}</label>
                <select name="notification_type" id="notification_type" class="form-control form-control-lg" required>
                    @if(auth()->user()->isRoot())
                        <option value="system" {{ old('notification_type') === 'system' ? 'selected' : '' }}>
                            {{ __('notification-create.type-system') }}
                        </option>
                    @endif
                    <option value="enterprise" {{ old('notification_type') === 'enterprise' ? 'selected' : '' }}>
                        {{ __('notification-create.type-enterprise') }}
                    </option>
                </select>

            </div>

            <!-- Doanh nghiệp -->
            <div class="mb-4">
                <label for="enterprise_id" class="form-label">{{ __('notification-create.enterprise-label') }}</label>
                <select name="enterprise_id" id="enterprise_id" class="form-control form-control-lg" {{ auth()->user()->isOwner() ? 'disabled' : '' }} required>
                    @if(auth()->user()->isRoot())
                        <option value="">{{ __('notification-create.enterprise-none') }}</option>
                        @foreach(\App\Domains\User\Enterprise\Model\Enterprise::all() as $enterprise)
                            <option value="{{ $enterprise->id }}" {{ old('enterprise_id') == $enterprise->id ? 'selected' : '' }}>
                                {{ $enterprise->name }}
                            </option>
                        @endforeach
                    @else
                        <option value="{{ auth()->user()->enterprise_id }}">
                            {{ \App\Domains\User\Enterprise\Model\Enterprise::find(auth()->user()->enterprise_id)?->name }}
                        </option>
                    @endif
                </select>

            </div>

            <!-- Nhóm mục tiêu -->
            <div class="mb-4">
                <label for="target_group" class="form-label">{{ __('notification-create.target-group-label') }}</label>
                <select name="target_group" id="target_group" class="form-control form-control-lg">
                    <option value="">{{ __('notification-create.target-group-all') }}</option>
                    @foreach(\App\Domains\User\Role\Model\Role::all() as $role)
                        <option value="{{ $role->name }}" {{ old('target_group') === $role->name ? 'selected' : '' }}>
                            {{ $role->name }}
                        </option>
                    @endforeach
                </select>

            </div>

            <!-- Người dùng cụ thể -->
            <div class="mb-4">
                <label for="user_ids" class="form-label">{{ __('notification-create.user-ids-label') }}</label>
                <select name="user_ids[]" id="user_ids" multiple class="form-control form-control-lg">
                    @foreach(auth()->user()->isRoot() ? \App\Domains\User\Model\User::all() : \App\Domains\User\Model\User::where('enterprise_id', auth()->user()->enterprise_id)->get() as $user)
                        <option value="{{ $user->id }}" {{ in_array($user->id, old('user_ids', [])) ? 'selected' : '' }}>
                            {{ $user->name }} ({{ $user->email }})
                        </option>
                    @endforeach
                </select>

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