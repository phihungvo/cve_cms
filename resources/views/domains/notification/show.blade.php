@extends('layouts.in')

@section('title', __('notification-show.title'))

@section('body')
    <div class="intro-y box p-5">
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4 p-4">{{ session('error') }}</div>
        @endif

        <h2 class="text-2xl font-medium mb-5">{{ __('notification-show.title') }}</h2>

        <div class="mb-4">
            <strong>{{ __('Title') }}:</strong> {{ $notification['title'] ?? '-' }}
        </div>
        <div class="mb-4">
            <strong>{{ __('Content') }}:</strong>
            <p>{{ $notification['content'] ?? '-' }}</p>
        </div>
        <div class="mb-4">
            <strong>{{ __('Type') }}:</strong>
            {{ $notification['notification_type'] === 'system' ? __('System') : __('Enterprise') }}
        </div>
        <div class="mb-4">
            <strong>{{ __('Enterprise') }}:</strong> {{ $notification['enterprise_name'] ?? 'N/A' }}
        </div>
        <div class="mb-4">
            <strong>{{ __('Target Group') }}:</strong> {{ $notification['target_group'] ?? 'All' }}
        </div>
        <div class="mb-4">
            <strong>{{ __('Sender') }}:</strong> {{ $notification['sender_name'] ?? 'N/A' }}
        </div>
        <div class="mb-4">
            <strong>{{ __('Created At') }}:</strong> {{ $notification['created_at'] }}
        </div>
        <div class="mb-4">
            <strong>{{ __('Read Status') }}:</strong>
            @if($notification['read_at'])
                <span class="text-success">{{ __('Read') }} ({{ $notification['read_at'] }})</span>
            @else
                <span class="text-danger">{{ __('Unread') }}</span>
            @endif
        </div>
        @if(auth()->check() && (auth()->user()->isRoot() || auth()->user()->id === $notification['sender_id']))
            <div class="mb-4">
                <strong>{{ __('notification-show.read-stats') }}:</strong>
                {{ $notification['read_count'] }}/{{ $notification['total_count'] }}
            </div>
        @endif

        <div class="mt-6">
            <a href="{{ route('notification.index') }}" class="btn btn-secondary mr-2">
                {{ __('Back to List') }}
            </a>
        </div>
    </div>
@endsection