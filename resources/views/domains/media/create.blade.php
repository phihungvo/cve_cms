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

            <!-- Progress Bar -->
            <div id="progress-container" class="mt-4 hidden">
                <label>{{ __('Uploading...') }}</label>
                <div class="w-full bg-gray-200 rounded-full h-4">
                    <div id="progress-bar" class="bg-blue-600 h-4 rounded-full"
                        style="width: 0%; transition: width 0.3s ease;"></div>
                </div>
                <p id="progress-text" class="text-sm text-gray-600 mt-1">0%</p>
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

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const uploadForm = document.querySelector('form[action="{{ route('fpp.media.create') }}"]');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            if (!uploadForm || !progressContainer || !progressBar || !progressText) {
                console.error('Form or progress elements not found');
                return;
            }

            uploadForm.addEventListener('submit', function (event) {
                event.preventDefault();
                console.log('Form submitted, preparing AJAX request');

                // Show progress bar
                progressContainer.classList.remove('hidden');
                progressBar.style.width = '0%';
                progressText.textContent = '0%';

                // Create FormData
                const formData = new FormData(uploadForm);
                const xhr = new XMLHttpRequest();

                // Progress event
                xhr.upload.addEventListener('progress', function (event) {
                    if (event.lengthComputable) {
                        const percentComplete = Math.round((event.loaded / event.total) * 100);
                        progressBar.style.width = percentComplete + '%';
                        progressText.textContent = percentComplete + '%';
                    }
                });

                // Completion event
                xhr.addEventListener('load', function () {
                    progressContainer.classList.add('hidden');
                    progressText.textContent = 'Processing...';
                    console.log('Request completed with status:', xhr.status);
                    console.log('Response:', xhr.responseText);
                    if (xhr.status === 200) {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (response.success) {
                                console.log('Upload successful, redirecting...');
                                window.location.href = "{{ route('fpp.media.index') }}";
                            } else {
                                showError(`Error ${xhr.status}: ${response.message || 'Upload failed'}`);
                            }
                        } catch (e) {
                            showError(`Error ${xhr.status}: Invalid response format`);
                        }
                    } else {
                        try {
                            const response = JSON.parse(xhr.responseText);
                            showError(`Error ${xhr.status}: ${response.message || 'Upload failed'}`);
                        } catch (e) {
                            showError(`Error ${xhr.status}: Upload failed`);
                        }
                    }
                });

                // Error event
                xhr.addEventListener('error', function () {
                    progressContainer.classList.add('hidden');
                    console.error('Network error occurred');
                    showError('Upload failed: Network error');
                });

                // Send request
                console.log('Sending request to:', uploadForm.action);
                xhr.open('POST', uploadForm.action, true);
                xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('meta[name="csrf-token"]').content);
                xhr.send(formData);
            });

            function showError(message) {
                console.error('Error displayed:', message);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mb-4';
                errorDiv.textContent = 'Error: ' + message;
                uploadForm.parentElement.insertBefore(errorDiv, uploadForm);
                setTimeout(() => errorDiv.remove(), 5000);
            }
        });
    </script>
@endpush