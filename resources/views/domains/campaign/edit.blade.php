@extends('layouts.in')

@section('body')
    <div class="tab-content">
        <div class="tab-pane active" role="tabpanel">
            <!-- Form Upload Media -->
            <form method="POST" action="{{ route('fpp.media.create') }}" enctype="multipart/form-data" id="upload-form">
                @csrf
                <input type="file" name="media_files[]" id="media-files" class="hidden" accept="video/mp4" multiple
                    required>
                <button type="button" class="upload-btn" id="upload-button"
                    onclick="document.getElementById('media-files').click();" title="{{ __('media-index.create') }}">
                    <span>{{ __('Upload') }}</span>
                </button>
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

                    <!-- Users (Quan hệ nhiều-nhiều với select box) -->
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
                        <label class="form-label">{{ __('campaign-index.media') }}</label>
                        <div id="upload-form-container"></div>
                        <select id="media-select" class="form-control" multiple>
                            <option value="">Select a Media</option>
                            @foreach ($media as $mediaItem)
                                <option value="{{ $mediaItem->id }}" data-name="{{ $mediaItem->name }}"
                                    data-type="{{ $mediaItem->type }}" data-url="{{ $mediaItem->media_url }}">
                                    {{ $mediaItem->name }} ({{ $mediaItem->type }})
                                </option>
                            @endforeach
                        </select>
                        <div id="selected-media"
                            class="mt-2 grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                            @foreach (old('media_ids', $campaign['media_ids'] ?? []) as $mediaId)
                                @php
                                    $mediaItem = $media->find($mediaId);
                                @endphp
                                @if ($mediaItem)
                                    @php
                                        $type = strtolower($mediaItem->type);
                                        $isVideo = str_contains($type, 'video') || in_array($type, ['mp4']);
                                    @endphp
                                    <div class="card mb-3 media bg-white selected-media" data-id="{{ $mediaItem->id }}"
                                        onmouseover="this.style.backgroundColor='#f0f0f0';"
                                        onmouseout="this.style.backgroundColor='white';">
                                        <div class="shadow-md rounded-lg overflow-hidden">
                                            <div class="p-4 flex flex-col items-center">
                                                <h5 class="text-lg font-bold text-center mb-2">{{ $mediaItem->name }}</h5>
                                                <p class="text-gray-500 mb-2">{{ $mediaItem->type }}</p>
                                                <div class="media-preview mb-2">
                                                    @if ($isVideo)
                                                        <video src="{{ $mediaItem->media_url }}" controls
                                                            style="max-width: 150px; max-height: 150px;"></video>
                                                    @else
                                                        <a href="{{ $mediaItem->media_url }}"
                                                            target="_blank">{{ __('View File') }}</a>
                                                    @endif
                                                </div>
                                                <button type="button" class="btn btn-danger btn-sm remove-media">X</button>
                                                <input type="hidden" name="media_ids[]" value="{{ $mediaItem->id }}">
                                            </div>
                                        </div>
                                    </div>
                                @endif
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

                    <!-- Reach, Impression, Distance, Budget -->
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
        .selected-user,
        .selected-media {
            margin: 5px 0;
        }

        .selected-user .remove-user,
        .selected-media .remove-media {
            margin-top: 8px;
            background-color: #1F2A44;
            /* Màu xanh dương đậm từ hình */
            border-color: #1F2A44;
        }

        .selected-user .remove-user:hover,
        .selected-media .remove-media:hover {
            background-color: #2E3B5A;
            /* Màu xanh dương đậm hơn khi hover */
            border-color: #2E3B5A;
        }

        .upload-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #1F2A44;
            /* Màu xanh dương đậm từ hình */
            color: white;
            font-size: 1.2rem;
            font-weight: bold;
            padding: 12px 24px;
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .upload-btn:hover {
            background-color: #2E3B5A;
            /* Màu xanh dương đậm hơn khi hover */
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        .upload-btn:active {
            transform: translateY(0);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .upload-btn span {
            margin-left: 8px;
        }

        /* Tăng chiều cao cho select multiple để hiển thị nhiều tùy chọn */
        #media-select {
            height: 150px;
            /* Chiều cao cố định để hiển thị nhiều mục */
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

                    this.value = ''; // Reset select về mặc định
                });

                // Xóa user đã chọn
                selectedUsersDiv.querySelectorAll('.remove-user').forEach(button => {
                    button.addEventListener('click', function() {
                        this.parentElement.remove();
                    });
                });
            }

            // Logic chọn media (hỗ trợ chọn nhiều)
            const mediaSelect = document.getElementById('media-select');
            const selectedMediaDiv = document.getElementById('selected-media');

            if (mediaSelect && selectedMediaDiv) {
                mediaSelect.addEventListener('change', function() {
                    const selectedOptions = Array.from(this
                    .selectedOptions); // Lấy tất cả các tùy chọn được chọn

                    selectedOptions.forEach(option => {
                        const mediaId = option.value;
                        const mediaName = option.dataset.name;
                        const mediaType = option.dataset.type;
                        const mediaUrl = option.dataset.url;
                        const isVideo = mediaType && (mediaType.toLowerCase().includes('video') ||
                            mediaType === 'mp4');

                        // Chỉ thêm media nếu chưa có trong selected-media
                        if (mediaId && !selectedMediaDiv.querySelector(`[data-id="${mediaId}"]`)) {
                            const mediaDiv = document.createElement('div');
                            mediaDiv.className = 'card mb-3 media bg-white selected-media';
                            mediaDiv.dataset.id = mediaId;
                            mediaDiv.setAttribute('onmouseover',
                                "this.style.backgroundColor='#f0f0f0';");
                            mediaDiv.setAttribute('onmouseout',
                                "this.style.backgroundColor='white';");
                            mediaDiv.innerHTML = `
                                <div class="shadow-md rounded-lg overflow-hidden">
                                    <div class="p-4 flex flex-col items-center">
                                        <h5 class="text-lg font-bold text-center mb-2">${mediaName || 'Unnamed Media'}</h5>
                                        <p class="text-gray-500 mb-2">${mediaType || 'Unknown'}</p>
                                        <div class="media-preview mb-2">
                                            ${isVideo ?
                                                `<video src="${mediaUrl}" controls style="max-width: 150px; max-height: 150px;"></video>` :
                                                `<a href="${mediaUrl}" target="_blank">View File</a>`}
                                        </div>
                                        <button type="button" class="btn btn-danger btn-sm remove-media">X</button>
                                        <input type="hidden" name="media_ids[]" value="${mediaId}">
                                    </div>
                                </div>
                            `;
                            selectedMediaDiv.appendChild(mediaDiv);

                            // Gắn sự kiện xóa cho nút X
                            mediaDiv.querySelector('.remove-media').addEventListener('click',
                                function() {
                                    mediaDiv.remove();
                                });
                        }
                    });

                    // Reset lựa chọn trong dropdown
                    this.selectedIndex = -1; // Bỏ chọn tất cả
                });

                // Gắn sự kiện xóa cho các media đã chọn ban đầu
                selectedMediaDiv.querySelectorAll('.remove-media').forEach(button => {
                    button.addEventListener('click', function() {
                        this.closest('.selected-media').remove();
                    });
                });
            }

            @if ($isRoot)
                const enterpriseSelect = document.querySelector('select[name="enterprise_id"]');
                if (enterpriseSelect && mediaSelect && selectedMediaDiv && userSelect) {
                    enterpriseSelect.addEventListener('change', function() {
                        const enterpriseId = this.value;
                        if (enterpriseId) {
                            // Tải danh sách media
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
                                    mediaSelect.innerHTML = '<option value="">Select a Media</option>';
                                    selectedMediaDiv.innerHTML = '';
                                    if (!data.data || data.data.length === 0) {
                                        mediaSelect.innerHTML +=
                                            '<option value="">No media available</option>';
                                        return;
                                    }

                                    data.data.forEach(media => {
                                        mediaSelect.innerHTML += `
                                            <option value="${media.id}"
                                                data-name="${media.name}"
                                                data-type="${media.type}"
                                                data-url="${media.media_url}">
                                                ${media.name} (${media.type})
                                            </option>`;
                                    });

                                    // Khôi phục media đã chọn của chiến dịch
                                    @if (!empty($campaign['media_ids']))
                                        const campaignMediaIds = @json($campaign['media_ids']);
                                        campaignMediaIds.forEach(mediaId => {
                                            const option = mediaSelect.querySelector(
                                                `option[value="${mediaId}"]`);
                                            if (option) {
                                                const mediaName = option.dataset.name;
                                                const mediaType = option.dataset.type;
                                                const mediaUrl = option.dataset.url;
                                                const isVideo = mediaType && (mediaType
                                                    .toLowerCase().includes('video') ||
                                                    mediaType === 'mp4');
                                                const mediaDiv = document.createElement('div');
                                                mediaDiv.className =
                                                    'card mb-3 media bg-white selected-media';
                                                mediaDiv.dataset.id = mediaId;
                                                mediaDiv.setAttribute('onmouseover',
                                                    "this.style.backgroundColor='#f0f0f0';");
                                                mediaDiv.setAttribute('onmouseout',
                                                    "this.style.backgroundColor='white';");
                                                mediaDiv.innerHTML = `
                                                    <div class="shadow-md rounded-lg overflow-hidden">
                                                        <div class="p-4 flex flex-col items-center">
                                                            <h5 class="text-lg font-bold text-center mb-2">${mediaName || 'Unnamed Media'}</h5>
                                                            <p class="text-gray-500 mb-2">${mediaType || 'Unknown'}</p>
                                                            <div class="media-preview mb-2">
                                                                ${isVideo ?
                                                                    `<video src="${mediaUrl}" controls style="max-width: 150px; max-height: 150px;"></video>` :
                                                                    `<a href="${mediaUrl}" target="_blank">View File</a>`}
                                                            </div>
                                                            <button type="button" class="btn btn-danger btn-sm remove-media">X</button>
                                                            <input type="hidden" name="media_ids[]" value="${mediaId}">
                                                        </div>
                                                    </div>
                                                `;
                                                selectedMediaDiv.appendChild(mediaDiv);

                                                mediaDiv.querySelector('.remove-media')
                                                    .addEventListener('click', function() {
                                                        mediaDiv.remove();
                                                    });
                                            }
                                        });
                                    @endif
                                })
                                .catch(error => {
                                    console.error('Error fetching media:', error);
                                    mediaSelect.innerHTML =
                                        '<option value="">Error loading media</option>';
                                });

                            // Tải danh sách users
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
                            mediaSelect.innerHTML = '<option value="">Select a Media</option>';
                            selectedMediaDiv.innerHTML = '';
                            userSelect.innerHTML = '<option value="">Select a User</option>';
                        }
                    });

                    // Kích hoạt sự kiện change ngay khi tải trang
                    enterpriseSelect.dispatchEvent(new Event('change'));
                }
            @endif
        });
    </script>
@endpush
