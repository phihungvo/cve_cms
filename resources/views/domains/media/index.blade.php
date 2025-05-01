@extends('layouts.in')

@section('title', __('media-index.title'))

@section('body')
    <div class="intro-y box p-5">
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4 p-4">{{ session('error') }}</div>
        @endif

        <!-- Search Form and Upload Button -->
        <div class="sm:flex sm:space-x-4">
            <form method="get" class="flex-grow mt-2 sm:mt-0">
                <input type="search" name="search" class="form-control form-control-lg"
                    placeholder="{{ __('media-index.filter') }}" data-table-search="#media-list-table"
                    value="{{ request('search') }}" />
            </form>
            <form method="POST" action="{{ route('fpp.media.create') }}" enctype="multipart/form-data" id="upload-form"
                class="sm:ml-4 mt-2 sm:mt-0 bg-white">
                @csrf
                <!-- Hidden fields for required data -->
                <input type="hidden" name="name" value="Uploaded Media {{ now()->format('Y-m-d H:i:s') }}">
                @if(auth()->user()->hasRole('root'))
                    <input type="hidden" name="enterprise_id" value="">
                @else
                    <input type="hidden" name="enterprise_id" value="{{ auth()->user()->enterprise_id }}">
                @endif
                <input type="file" name="media_files[]" id="media-files" class="hidden" accept="video/mp4" multiple required>
                <button type="button" class="btn form-control-lg whitespace-nowrap"
                    onclick="document.getElementById('media-files').click();">
                    {{ __('media-index.create') }}
                </button>
            </form>
        </div>

        <!-- Progress Bar -->
        <div id="progress-container" class="mt-4 hidden">
            <label>{{ __('Uploading...') }}</label>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div id="progress-bar" class="bg-blue-600 h-4 rounded-full" style="width: 0%; transition: width 0.3s ease;"></div>
            </div>
            <p id="progress-text" class="text-sm text-gray-600 mt-1">0%</p>
        </div>

        <!-- Table -->
        <div class="overflow-auto scroll-visible header-sticky mt-5">
            <table id="media-list-table" class="table table-report sm:mt-2 font-medium text-center whitespace-nowrap"
                data-table-sort data-table-pagination data-table-pagination-limit="10">
                <thead>
                    <tr>
                        <th class="w-1">{{ __('STT') }}</th>
                        <th>{{ __('Media Name') }}</th>
                        <th>{{ __('Media File') }}</th>
                        <th>{{ __('Size') }}</th>
                        <th>{{ __('Duration') }}</th>
                        <th>{{ __('Enterprise') }}</th>
                        <th>{{ __('Created At') }}</th>
                        <th>{{ __('Updated At') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($media as $key => $item)
                        <tr>
                            <td class="w-1">{{ $key + 1 }}</td>
                            <td>{{ $item['name'] ?? '-' }}</td>
                            <td>
                                @php
                                    $type = strtolower($item['type']);
                                    $isVideo = str_contains($type, 'video') || in_array($type, ['mp4']);
                                @endphp
                                @if ($isVideo)
                                    <video src="{{ $item['media_url'] }}" controls
                                        style="max-width: 100px; max-height: 100px;"></video>
                                @else
                                    <a href="{{ $item['media_url'] }}" target="_blank">{{ __('View File') }}</a>
                                @endif
                            </td>
                            <td>{{ number_format($item['size'] / (1024 * 1024), 2) }} MB</td>
                            <td>{{ $item['duration'] ? $item['duration'] . 's' : 'N/A' }}</td>
                            <td>
                                @if($item['enterprise_id'])
                                    {{ \App\Domains\User\Enterprise\Model\Enterprise::find($item['enterprise_id'])->name ?? 'N/A' }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ \Carbon\Carbon::createFromTimestamp($item['created_at'])->format('Y-m-d H:i:s') }}</td>
                            <td>{{ $item['updated_at'] ? \Carbon\Carbon::createFromTimestamp($item['updated_at'])->format('Y-m-d H:i:s') : 'N/A' }}</td>
                            <td>{{ $item['deleted_at'] ? __('Deleted') : __('Active') }}</td>
                            <td>
                                @if(auth()->user()->hasRole('root'))
                                    @if($item['deleted_at'])
                                        <form action="{{ route('fpp.media.restore', $item['id']) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="btn btn-success btn-sm mr-2">{{ __('Restore') }}</button>
                                        </form>
                                        <form action="{{ route('fpp.media.force-delete', $item['id']) }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('{{ __('Are you sure you want to permanently delete this media?') }}')">{{ __('Force Delete') }}</button>
                                        </form>
                                    @else
                                        <a href="javascript:;" data-toggle="modal" data-target="#rename-modal"
                                            onclick="document.getElementById('rename-media-id').value = '{{ $item['id'] }}'; document.getElementById('rename-media-name').value = '{{ addslashes($item['name'] ?? '-') }}';"
                                            class="btn btn-primary btn-sm mr-2">{{ __('Rename') }}</a>
                                        <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                            onclick="document.getElementById('delete-media-id').value = '{{ addslashes($item['media_url']) }}'; document.getElementById('delete-media-name').innerText = '{{ addslashes($item['name'] ?? '-') }}';"
                                            class="btn btn-danger btn-sm">{{ __('Delete') }}</a>
                                    @endif
                                @else
                                    @if(!$item['deleted_at'])
                                        <a href="javascript:;" data-toggle="modal" data-target="#rename-modal"
                                            onclick="document.getElementById('rename-media-id').value = '{{ $item['id'] }}'; document.getElementById('rename-media-name').value = '{{ addslashes($item['name'] ?? '-') }}';"
                                            class="btn btn-primary btn-sm mr-2">{{ __('Rename') }}</a>
                                        <a href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                            onclick="document.getElementById('delete-media-id').value = '{{ addslashes($item['media_url']) }}'; document.getElementById('delete-media-name').innerText = '{{ addslashes($item['name'] ?? '-') }}';"
                                            class="btn btn-danger btn-sm">{{ __('Delete') }}</a>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">{{ __('No media found') }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Delete Modal -->
    @include('molecules.delete-modal', [
        'method' => 'delete',
        'route' => route('fpp.media.delete', 0),
        'title' => __('media-delete.title'),
        'message' => '<span id="delete-media-name"></span>',
    ])

    <!-- Rename Modal -->
    @include('molecules.rename-modal', [
        'method' => 'put',
        'route' => route('fpp.media.rename'),
        'title' => __('media-rename.title'),
        'message' => __('media-rename.message'),
    ])

@endsection

@push('styles')
    <style>
        .alert.alert-danger {
            background: #f8d7da;
            color: #721c24;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #f5c6cb;
            display: block !important;
            z-index: 1000;
            position: relative;
        }
        .alert.alert-success {
            background: #d4edda;
            color: #155724;
            padding: 1rem;
            margin-bottom: 1rem;
            border: 1px solid #c3e6cb;
            display: block !important;
            z-index: 1000;
            position: relative;
        }
    </style>
@endpush

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Delete modal
            const deleteForm = document.querySelector('#delete-modal form');
            if (deleteForm) {
                const deleteInput = document.createElement('input');
                deleteInput.type = 'hidden';
                deleteInput.name = 'media_url';
                deleteInput.id = 'delete-media-id';
                deleteForm.appendChild(deleteInput);
            } else {
                console.error('Delete modal form not found');
            }

            // Upload form
            const fileInput = document.getElementById('media-files');
            const uploadForm = document.getElementById('upload-form');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            if (!progressContainer || !progressBar || !progressText) {
                console.error('Progress bar elements not found');
                return;
            }

            if (!uploadForm) {
                console.error('Upload form not found');
                return;
            }

            fileInput.addEventListener('change', function() {
                if (fileInput.files.length > 0) {
                    // Disable upload button
                    const uploadButton = uploadForm.querySelector('button');
                    uploadButton.disabled = true;

                    // Show progress bar
                    progressContainer.classList.remove('hidden');
                    progressBar.style.width = '0%';
                    progressText.textContent = '0%';

                    // Create FormData
                    const formData = new FormData(uploadForm);
                    const xhr = new XMLHttpRequest();

                    // Progress event
                    xhr.upload.addEventListener('progress', function(event) {
                        if (event.lengthComputable) {
                            const percentComplete = Math.round((event.loaded / event.total) * 100);
                            progressBar.style.width = percentComplete + '%';
                            progressText.textContent = percentComplete + '%';
                        }
                    });

                    // Completion event
                    xhr.addEventListener('load', function() {
                        progressContainer.classList.add('hidden');
                        uploadButton.disabled = false;
                        fileInput.value = ''; // Clear file input

                        console.log('Response Status:', xhr.status);
                        console.log('Response Text:', xhr.responseText);

                        try {
                            const response = JSON.parse(xhr.responseText);

                            if (xhr.status === 200 && response.success) {
                                // Full success
                                showSuccess(response.message || '{{ __('media-create.upload-success', ['count' => '__COUNT__']) }}'.replace('__COUNT__', response.data.length));
                                setTimeout(() => window.location.reload(), 2000);
                            } else if (xhr.status === 207) {
                                // Partial success
                                if (response.data && response.data.length > 0) {
                                    showSuccess('{{ __('media-create.upload-success', ['count' => '__COUNT__']) }}'.replace('__COUNT__', response.data.length));
                                }
                                if (response.errors && response.errors.length > 0) {
                                    response.errors.forEach(error => {
                                        showError(`File ${error.file}: ${error.error}`);
                                    });
                                }
                                setTimeout(() => window.location.reload(), 3000);
                            } else if (xhr.status === 422) {
                                // Validation errors
                                if (response.errors) {
                                    Object.keys(response.errors).forEach(key => {
                                        response.errors[key].forEach(msg => {
                                            showError(`${key}: ${msg}`);
                                        });
                                    });
                                } else {
                                    showError(response.message || 'Validation failed');
                                }
                            } else {
                                // General error (including 500)
                                showError(response.message || 'Upload failed: Server error');
                            }
                        } catch (e) {
                            console.error('Parse Error:', e, 'Response:', xhr.responseText);
                            showError('Server error: ' + (xhr.responseText.substring(0, 100) || 'Unknown error'));
                        }
                    });

                    // Error event (network issues)
                    xhr.addEventListener('error', function() {
                        progressContainer.classList.add('hidden');
                        uploadButton.disabled = false;
                        fileInput.value = '';
                        showError('Network error during upload');
                    });

                    // Send request
                    xhr.open('POST', uploadForm.action, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.send(formData);
                }
            });

            function showSuccess(message) {
                console.log('Displaying Success:', message);
                const successDiv = document.createElement('div');
                successDiv.className = 'alert alert-success mb-4 p-4';
                successDiv.style.display = 'block';
                successDiv.style.zIndex = '1000';
                successDiv.style.position = 'relative';
                successDiv.textContent = message;
                const container = document.querySelector('.intro-y.box.p-5') || document.body;
                container.insertBefore(successDiv, container.firstChild);
                setTimeout(() => successDiv.remove(), 5000);
            }

            function showError(message) {
                console.error('Displaying Error:', message);
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-danger mb-4 p-4';
                errorDiv.style.display = 'block';
                errorDiv.style.zIndex = '1000';
                errorDiv.style.position = 'relative';
                errorDiv.textContent = 'Error: ' + message;
                const container = document.querySelector('.intro-y.box.p-5') || document.body;
                container.insertBefore(errorDiv, container.firstChild);
                setTimeout(() => errorDiv.remove(), 10000);
            }
        });
    </script>
@endpush