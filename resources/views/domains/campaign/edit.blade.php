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
                    <!-- Name, Enterprise, User -->
                    <div class="row align-items-start">
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.name') }}</label>
                                <input type="text" name="name" class="form-control"
                                    value="{{ old('name', $campaign['name']) }}" required>
                            </div>
                        </div>
                        <div class="col-md-4">
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
                        <div class="col-md-4">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.users') }}</label>
                                <select id="user-select" name="user_ids[]" class="form-control select2" multiple required>
                                    @foreach ($users as $user)
                                        <option value="{{ $user->id }}"
                                            {{ in_array($user->id, old('user_ids', $campaign['user_ids'] ?? [])) ? 'selected' : '' }}>
                                            {{ $user->name }} ({{ $user->email }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Media -->
                    <div class="form-group mb-3">
                        <div class="flex items-center justify-between mb-2">
                            <label class="form-label">{{ __('campaign-index.media') }}</label>
                            <a href="#" id="upload-link" class="text-blue-600 hover:underline"
                                onclick="document.getElementById('media-files').click();">{{ __('Upload') }}</a>
                        </div>
                        <div id="media-gallery" class="grid grid-cols-6 gap-4">
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
                    <div class="row">
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

                    <!-- Reach, Impression, Distance, Device, Budget, CPM, Location -->
                    <div class="row align-items-start">
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.reach') }}</label>
                                <input type="number" name="reach" class="form-control"
                                    value="{{ old('reach', $campaign['reach']) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.impression') }}</label>
                                <input type="number" name="impression" class="form-control"
                                    value="{{ old('impression', $campaign['impression']) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.distance') }}</label>
                                <input type="number" name="distance" class="form-control"
                                    value="{{ old('distance', $campaign['distance']) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.no_device') }}</label>
                                <input type="number" name="no_device" class="form-control"
                                    value="{{ old('no_device', $campaign['no_device']) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.budget') }}</label>
                                <input type="number" name="budget" class="form-control"
                                    value="{{ old('budget', $campaign['budget']) }}" step="0.01" min="0"
                                    required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.cpm') }}</label>
                                <input type="number" name="cpm" class="form-control"
                                    value="{{ old('cpm', $campaign['cpm']) }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.location') }}</label>
                                <select name="location_id" class="form-control select2" required>
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

        .grid-cols-6 {
            grid-template-columns: repeat(6, minmax(0, 1fr));
        }

        /* Đảm bảo layout ngang và loại bỏ khoảng trắng thừa */
        .row {
            display: flex !important;
            flex-wrap: wrap !important;
            margin-left: -5px !important;
            margin-right: -5px !important;
        }

        .row .col-md-4,
        .row .col-md-2 {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }

        .row .col-md-4 {
            flex: 0 0 33.3333% !important;
            max-width: 33.3333% !important;
        }

        .row .col-md-2 {
            flex: 0 0 14.2857% !important;
            max-width: 14.2857% !important;
        }

        @media (max-width: 767.98px) {

            .row .col-md-4,
            .row .col-md-2 {
                flex: 0 0 100% !important;
                max-width: 100% !important;
            }
        }

        /* Đảm bảo Select2 không phá vỡ layout */
        .select2-container {
            width: 100% !important;
            max-width: 100% !important;
            box-sizing: border-box !important;
        }

        .select2-container .select2-selection--multiple,
        .select2-container .select2-selection--single {
            height: auto !important;
            min-height: 38px !important;
            padding: 0 !important;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            margin: 2px !important;
            padding: 2px 5px !important;
        }
    </style>
@stop

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Khởi tạo Select2 cho User và Location
            $('#user-select').select2({
                placeholder: "Select a User",
                allowClear: true,
                width: '100%',
                dropdownParent: $('#user-select').parent() // Đảm bảo dropdown hiển thị đúng
            });
            $('select[name="location_id"]').select2({
                placeholder: "Select Location",
                allowClear: true,
                width: '100%',
                dropdownParent: $('select[name="location_id"]').parent()
            });

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

                    const video = mediaItem.querySelector('video');
                    if (video) {
                        mediaItem.addEventListener('mouseenter', function() {
                            video.play().catch(error => {
                                console.error('Error playing video:', error);
                            });
                        });

                        mediaItem.addEventListener('mouseleave', function() {
                            video.pause();
                            video.currentTime = 0;
                        });
                    }
                }

                mediaGallery.querySelectorAll('.media-item').forEach(setupMediaItem);
            }

            @if ($isRoot)
                const enterpriseSelect = document.querySelector('select[name="enterprise_id"]');
                if (enterpriseSelect && mediaGallery && $('#user-select').length) {
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
                                            media.id) ? 'checked' : '';
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
                                    $('#user-select').empty();
                                    $('#user-select').append('<option value="">Select a User</option>');
                                    if (!data.data || data.data.length === 0) {
                                        $('#user-select').append(
                                            '<option value="">No users available</option>');
                                    } else {
                                        data.data.forEach(user => {
                                            $('#user-select').append(
                                                `<option value="${user.id}">${user.name} (${user.email})</option>`
                                                );
                                        });
                                    }
                                    $('#user-select').val(@json(old('user_ids', $campaign['user_ids'] ?? []))).trigger(
                                        'change');
                                })
                                .catch(error => {
                                    console.error('Error fetching users:', error);
                                    $('#user-select').html('<option value="">Error loading users: ' +
                                        error.message + '</option>');
                                });
                        } else {
                            mediaGallery.innerHTML =
                                '<div class="col-span-full p-2 text-gray-500">No media available</div>';
                            $('#user-select').empty().append('<option value="">Select a User</option>')
                                .trigger('change');
                        }
                    });

                    enterpriseSelect.dispatchEvent(new Event('change'));
                }
            @endif
        });
    </script>
@endpush
