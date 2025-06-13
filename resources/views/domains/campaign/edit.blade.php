@extends('layouts.in')

@section('body')
    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            <!-- Form Upload Media -->
            <form method="POST" action="{{ route('fpp.media.create') }}" enctype="multipart/form-data" id="upload-form">
                @csrf
                <input type="file" name="media_files[]" id="media-files" class="hidden" accept="video/mp4" multiple
                    required>
            </form>

            <!-- Form chính -->
            <form method="POST" action="{{ route('campaign.update', $campaign['id']) }}" class="needs-validation"
                novalidate>
                @csrf
                @method('PUT')
                <div class="box p-5">
                    <!-- Name -->
                    <div class="row justify-between">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.name') }}</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $campaign['name']) }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Enterprise -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.enterprise') }}</label>
                                @php
                                    $isRoot = auth()->check() && auth()->user()->isRoleRoot();
                                @endphp
                                @if ($isRoot)
                                    <select name="enterprise_id" class="form-control" required>
                                        <option value="">Select Enterprise</option>
                                        @foreach ($enterprises as $enterprise)
                                            <option value="{{ $enterprise->id }}"
                                                {{ old('enterprise_id', $campaign['enterprise_id']) == $enterprise->id ? 'selected' : '' }}>
                                                {{ $enterprise->name }}
                                                @if ($campaign['enterprise_id'] == $enterprise->id)
                                                    <span style="color: green;"> ✓</span>
                                                @endif
                                            </option>
                                        @endforeach
                                    </select>
                                @else
                                    <input type="text" class="form-control"
                                        value="{{ auth()->user()->enterprise->name ?? 'N/A' }}" readonly>
                                    <input type="hidden" name="enterprise_id" value="{{ auth()->user()->enterprise_id }}">
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Users -->
                    <div class="form-group mb-3">
                        <label class="form-label">{{ __('campaign-index.users') }}</label>
                        <select id="user-select" class="form-control">
                            <option value="">Select a User</option>
                            @foreach ($users as $user)
                                <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                            @endforeach
                        </select>
                        <div id="selected-users" class="mt-2">
                            @foreach (old('user_ids', $campaign['user_ids'] ?? []) as $userId)
                                @php
                                    $user = $users->find($userId);
                                @endphp
                                @if ($user)
                                    <div class="selected-user" data-id="{{ $user->id }}">
                                        {{ $user->name }} ({{ $user->email }})
                                        <button type="button" class="btn btn-danger btn-sm remove-user">X</button>
                                        <input type="hidden" name="user_ids[]" value="{{ $user->id }}">
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <!-- Media -->
                    <div class="form-group mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <label class="form-label">{{ __('campaign-index.media') }}</label>
                            <a href="#" id="upload-link" class="text-blue-600 hover:underline"
                                onclick="document.getElementById('media-files').click();">{{ __('Upload') }}</a>
                        </div>
                        <div id="media-gallery" class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                            @foreach ($media as $mediaItem)
                                @php
                                    $type = strtolower($mediaItem->type);
                                    $isVideo = str_contains($type, 'video') || in_array($type, ['mp4']);
                                @endphp
                                <div class="media-item border rounded p-2 cursor-pointer" data-id="{{ $mediaItem->id }}"
                                    data-name="{{ $mediaItem->name }}" data-type="{{ $mediaItem->type }}"
                                    data-url="{{ $mediaItem->media_url }}">
                                    <div class="flex items-center mb-2">
                                        <input type="checkbox" name="media_ids[]" value="{{ $mediaItem->id }}"
                                            class="mr-2 media-checkbox"
                                            {{ in_array($mediaItem->id, old('media_ids', $campaign['media_ids'] ?? [])) ? 'checked' : '' }}>
                                        <span class="text-sm truncate">{{ $mediaItem->name }}
                                            ({{ $mediaItem->type }})</span>
                                    </div>
                                    <div class="media-preview">
                                        @if ($isVideo)
                                            <video src="{{ $mediaItem->media_url }}" class="w-full h-24 object-cover"
                                                muted></video>
                                        @else
                                            <a href="{{ $mediaItem->media_url }}" target="_blank"
                                                class="text-blue-600 hover:underline text-sm">{{ __('View File') }}</a>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Start Time và End Time -->
                    <div class="row justify-between">
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.start_time') }}</label>
                                <input type="datetime-local" name="start_time" class="form-control"
                                    value="{{ old('start_time', \Carbon\Carbon::parse($campaign['start_time'])->format('Y-m-d\TH:i')) }}"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.end_time') }}</label>
                                <input type="datetime-local" name="end_time" class="form-control"
                                    value="{{ old('end_time', \Carbon\Carbon::parse($campaign['end_time'])->format('Y-m-d\TH:i')) }}"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- Reach, Impression, Distance, No Device -->
                    <div class="row justify-between">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.reach') }}</label>
                                <input type="number" name="reach" class="form-control"
                                    value="{{ old('reach', $campaign['reach']) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.impression') }}</label>
                                <input type="number" name="impression" class="form-control"
                                    value="{{ old('impression', $campaign['impression']) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.distance') }}</label>
                                <input type="number" name="distance" class="form-control"
                                    value="{{ old('distance', $campaign['distance']) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.no_device') }}</label>
                                <input type="number" name="no_device" class="form-control"
                                    value="{{ old('no_device', $campaign['no_device']) }}" min="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- Budget -->
                    <div class="row justify-between">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.budget') }}</label>
                                <input type="number" name="budget" class="form-control"
                                    value="{{ old('budget', $campaign['budget']) }}" step="0.01" min="0"
                                    required>
                            </div>
                        </div>
                    </div>

                    <!-- CPM -->
                    <div class="row justify-between">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.cpm') }}</label>
                                <input type="number" name="cpm" class="form-control"
                                    value="{{ old('cpm', $campaign['cpm']) }}" min="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="row justify-between">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.location') }}</label>
                                <select name="location_id" class="form-control" required>
                                    <option value="">Select Location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}"
                                            {{ old('location_id', $campaign['location_id']) == $location->id ? 'selected' : '' }}>
                                            {{ $location->city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box p-5 mt-5 text-right">
                    <button type="submit" class="btn btn-primary">{{ __('campaign-edit.save') }}</button>
                    <a href="{{ route('campaign.index') }}" class="btn btn-secondary ml-2">{{ __('Cancel') }}</a>
                </div>
            </form>
        </div>
    </div>

    <style>
        .selected-user {
            margin: 5px 0;
        }

        .selected-user .remove-user {
            margin-top: 8px;
            background-color: #1F2A44;
            border-color: #1F2A44;
        }

        .selected-user .remove-user:hover {
            background-color: #2E3B5A;
            border-color: #2E3B5A;
        }

        .media-item {
            transition: transform 0.2s;
        }

        .media-item:hover {
            transform: scale(1.02);
        }

        .media-item.selected {
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.3);
        }

        .media-preview video {
            border-radius: 4px;
        }

        .grid-cols-2 {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }

        .md\:grid-cols-3 {
            @media (min-width: 768px) {
                grid-template-columns: repeat(3, minmax(0, 1fr));
            }
        }

        .lg\:grid-cols-4 {
            @media (min-width: 1024px) {
                grid-template-columns: repeat(4, minmax(0, 1fr));
            }
        }
    </style>
@stop

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadFormContainer = document.getElementById('upload-form-container');
            const uploadForm = document.getElementById('upload-form');
            if (uploadFormContainer && uploadForm) {
                uploadFormContainer.appendChild(uploadForm);
            }

            const forms = document.querySelectorAll('.needs-validation');
            forms.forEach(function(form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                }, false);
            });

            const budgetInput = document.querySelector('input[name="budget"]');
            const reachInput = document.querySelector('input[name="reach"]');
            const cpmInput = document.querySelector('input[name="cpm"]');

            function calculateCPM() {
                const budget = parseFloat(budgetInput.value) || 0;
                const reach = parseFloat(reachInput.value) || 0;
                if (reach > 0) {
                    const cpm = (budget / reach) * 1000;
                    cpmInput.value = Math.round(cpm);
                } else {
                    cpmInput.value = 0;
                }
            }

            if (budgetInput && reachInput) {
                budgetInput.addEventListener('input', calculateCPM);
                reachInput.addEventListener('input', calculateCPM);
            }

            const fileInput = document.getElementById('media-files');
            if (fileInput) {
                fileInput.addEventListener('change', function() {
                    if (fileInput.files.length > 0) {
                        document.getElementById('upload-form').submit();
                    }
                });
            }

            // Logic chọn user
            const userSelect = document.getElementById('user-select');
            const selectedUsersDiv = document.getElementById('selected-users');

            if (userSelect && selectedUsersDiv) {
                userSelect.addEventListener('change', function() {
                    const userId = this.value;
                    const userText = this.options[this.selectedIndex].text;

                    if (userId && !selectedUsersDiv.querySelector(`[data-id="${userId}"]`)) {
                        const userDiv = document.createElement('div');
                        userDiv.className = 'selected-user';
                        userDiv.dataset.id = userId;
                        userDiv.innerHTML = `
                            ${userText}
                            <button type="button" class="btn btn-danger btn-sm remove-user">X</button>
                            <input type="hidden" name="user_ids[]" value="${userId}">
                        `;
                        selectedUsersDiv.appendChild(userDiv);

                        userDiv.querySelector('.remove-user').addEventListener('click', function() {
                            userDiv.remove();
                        });
                    }

                    this.value = '';
                });

                selectedUsersDiv.querySelectorAll('.remove-user').forEach(button => {
                    button.addEventListener('click', function() {
                        this.parentElement.remove();
                    });
                });
            }

            // Logic chọn media và hover video
            const mediaGallery = document.getElementById('media-gallery');
            if (mediaGallery) {
                function updateMediaItemStyle(mediaItem) {
                    const checkbox = mediaItem.querySelector('.media-checkbox');
                    if (checkbox.checked) {
                        mediaItem.classList.add('selected');
                    } else {
                        mediaItem.classList.remove('selected');
                    }
                }

                function setupMediaItem(mediaItem) {
                    updateMediaItemStyle(mediaItem);

                    // Click to select
                    mediaItem.addEventListener('click', function(event) {
                        if (!event.target.classList.contains('media-checkbox')) {
                            const checkbox = mediaItem.querySelector('.media-checkbox');
                            checkbox.checked = !checkbox.checked;
                            updateMediaItemStyle(mediaItem);
                        }
                    });

                    mediaItem.querySelector('.media-checkbox').addEventListener('change', function() {
                        updateMediaItemStyle(mediaItem);
                    });

                    // Hover to play video
                    const video = mediaItem.querySelector('video');
                    if (video) {
                        mediaItem.addEventListener('mouseenter', function() {
                            video.play().catch(error => {
                                console.error('Error playing video:', error);
                            });
                        });

                        mediaItem.addEventListener('mouseleave', function() {
                            video.pause();
                            video.currentTime = 0; // Reset to start
                        });
                    }
                }

                mediaGallery.querySelectorAll('.media-item').forEach(setupMediaItem);
            }

            @if ($isRoot)
                const enterpriseSelect = document.querySelector('select[name="enterprise_id"]');
                if (enterpriseSelect && mediaGallery && userSelect) {
                    enterpriseSelect.addEventListener('change', function() {
                        const enterpriseId = this.value;
                        if (enterpriseId) {
                            fetch(`/campaign/media-by-enterprise/${enterpriseId}`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(
                                            `Media API error: ${response.status} ${response.statusText}`
                                        );
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    mediaGallery.innerHTML = '';
                                    if (!data.data || data.data.length === 0) {
                                        mediaGallery.innerHTML =
                                            '<div class="col-span-full p-2 text-gray-500">No media available</div>';
                                        return;
                                    }

                                    data.data.forEach(media => {
                                        const isChecked = @json($campaign['media_ids'] ?? []).includes(
                                                media.id) ?
                                            'checked' :
                                            '';
                                        const isVideo = media.type && (media.type.toLowerCase()
                                            .includes('video') || media.type === 'mp4');
                                        mediaGallery.innerHTML += `
                                            <div class="media-item border rounded p-2 cursor-pointer"
                                                data-id="${media.id}"
                                                data-name="${media.name}"
                                                data-type="${media.type}"
                                                data-url="${media.media_url}">
                                                <div class="flex items-center mb-2">
                                                    <input type="checkbox" name="media_ids[]" value="${media.id}"
                                                        class="mr-2 media-checkbox" ${isChecked}>
                                                    <span class="text-sm truncate">${media.name} (${media.type})</span>
                                                </div>
                                                <div class="media-preview">
                                                    ${isVideo
                                                        ? `<video src="${media.media_url}" class="w-full h-24 object-cover" muted></video>`
                                                        : `<a href="${media.media_url}" target="_blank" class="text-blue-600 hover:underline text-sm">View File</a>`}
                                                </div>
                                            </div>`;
                                    });

                                    mediaGallery.querySelectorAll('.media-item').forEach(
                                    setupMediaItem);
                                })
                                .catch(error => {
                                    console.error('Error fetching media:', error);
                                    mediaGallery.innerHTML =
                                        '<div class="col-span-full p-2 text-gray-500">Error loading media</div>';
                                });

                            fetch(`/campaign/users-by-enterprise/${enterpriseId}`, {
                                    headers: {
                                        'Accept': 'application/json',
                                        'X-Requested-With': 'XMLHttpRequest',
                                        'X-CSRF-TOKEN': document.querySelector(
                                            'meta[name="csrf-token"]').content
                                    }
                                })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error(
                                            `Users API error: ${response.status} ${response.statusText}`
                                        );
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    userSelect.innerHTML = '<option value="">Select a User</option>';
                                    if (!data.data || data.data.length === 0) {
                                        userSelect.innerHTML +=
                                            '<option value="">No users available</option>';
                                        return;
                                    }

                                    data.data.forEach(user => {
                                        userSelect.innerHTML += `
                                            <option value="${user.id}">${user.name} (${user.email})</option>`;
                                    });
                                })
                                .catch(error => {
                                    console.error('Error fetching users:', error);
                                    userSelect.innerHTML = '<option value="">Error loading users: ' +
                                        error.message + '</option>';
                                });
                        } else {
                            mediaGallery.innerHTML =
                                '<div class="col-span-full p-2 text-gray-500">No media available</div>';
                            userSelect.innerHTML = '<option value="">Select a User</option>';
                        }
                    });

                    enterpriseSelect.dispatchEvent(new Event('change'));
                }
            @endif
        });
    </script>
@endpush
