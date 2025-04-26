```blade
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
            <form method="POST" action="{{ route('campaign.store') }}" class="needs-validation" novalidate>
                @csrf
                <div class="box p-5">
                    <!-- Name -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class=" form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.name') }}</label>
                                <input type="text" name="name" class="form-control" value="{{ old('name') }}"
                                    required>
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
                                                {{ old('enterprise_id') == $enterprise->id ? 'selected' : '' }}>
                                                {{ $enterprise->name }}
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
                            @if (old('user_ids'))
                                @foreach (old('user_ids') as $userId)
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
                            @endif
                        </div>
                    </div>

                    <!-- Media -->
                    <div class="form-group mb-3">
                        <label class="form-label mb-0">{{ __('campaign-index.media') }}</label>
                        <div id="upload-form-container"></div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4"
                            id="media-container">
                            @foreach ($media as $mediaItem)
                                <div class="card mb-3 media bg-white" id="media-{{ $mediaItem->id }}"
                                    media-id="{{ $mediaItem->id }}" onmouseover="this.style.backgroundColor='#f0f0f0';"
                                    onmouseout="this.style.backgroundColor='white';">
                                    <div class="shadow-md rounded-lg overflow-hidden">
                                        <div class="p-4 flex flex-col items-center">
                                            <h5 class="text-lg font-bold text-center mb-2">{{ $mediaItem->name }}</h5>
                                            @php
                                                $type = strtolower($mediaItem->type);
                                                $isVideo = str_contains($type, 'video') || in_array($type, ['mp4']);
                                            @endphp
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
                                            <div class="mt-2">
                                                <label for="media-{{ $mediaItem->id }}"
                                                    class="form-check-label">{{ __('Select Media') }}</label>
                                                <input type="checkbox" name="media_ids[]" value="{{ $mediaItem->id }}"
                                                    class="form-check-switch" id="media-{{ $mediaItem->id }}"
                                                    {{ in_array($mediaItem->id, old('media_ids', [])) ? 'checked' : '' }}>
                                            </div>
                                        </div>
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
                                    value="{{ old('start_time') }}" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.end_time') }}</label>
                                <input type="datetime-local" name="end_time" class="form-control"
                                    value="{{ old('end_time') }}" required>
                            </div>
                        </div>
                    </div>

                    <!-- Reach, Impression, Distance, Budget -->
                    <div class="row justify-between">
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.reach') }}</label>
                                <input type="number" name="reach" class="form-control" value="{{ old('reach') }}"
                                    min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.impression') }}</label>
                                <input type="number" name="impression" class="form-control"
                                    value="{{ old('impression') }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.distance') }}</label>
                                <input type="number" name="distance" class="form-control"
                                    value="{{ old('distance') }}" min="0" required>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.budget') }}</label>
                                <input type="number" name="budget" class="form-control" value="{{ old('budget') }}"
                                    step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- CPM -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.cpm') }}</label>
                                <input type="number" name="cpm" class="form-control" value="{{ old('cpm') }}"
                                    min="0" required>
                            </div>
                        </div>
                    </div>

                    <!-- Location -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group mb-3">
                                <label class="form-label">{{ __('campaign-index.location') }}</label>
                                <select name="location_id" class="form-control" required>
                                    <option value="">Select Location</option>
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->id }}"
                                            {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                            {{ $location->city }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="box p-5 mt-5">
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">{{ __('campaign-create.save') }}</button>
                        <a href="{{ route('campaign.index') }}" class="btn btn-secondary ml-2">{{ __('Cancel') }}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <style>
        .selected-user {
            display: flex;
            align-items: center;
            margin: 5px 0;
            padding: 5px;
            background-color: #f0f0f0;
            border-radius: 4px;
        }

        .selected-user .remove-user {
            margin-left: 10px;
        }

        .upload-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background-color: #ff5733;
            /* Vibrant orange color */
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
            background-color: #e64a29;
            /* Darker shade on hover */
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
    </style>
@stop

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const uploadFormContainer = document.getElementById('upload-form-container');
            const uploadForm = document.getElementById('upload-form');
            uploadFormContainer.appendChild(uploadForm);

            var forms = document.querySelectorAll('.needs-validation');
            Array.prototype.slice.call(forms).forEach(function(form) {
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

            budgetInput.addEventListener('input', calculateCPM);
            reachInput.addEventListener('input', calculateCPM);

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

            @if ($isRoot)
                const enterpriseSelect = document.querySelector('select[name="enterprise_id"]');
                const mediaContainer = document.querySelector('#media-container');

                enterpriseSelect.addEventListener('change', function() {
                    const enterpriseId = this.value;
                    if (enterpriseId) {
                        fetch(`/campaign/media-by-enterprise/${enterpriseId}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                mediaContainer.innerHTML = '';
                                if (!data.data || data.data.length === 0) {
                                    mediaContainer.innerHTML =
                                        '<p>No media available for this enterprise.</p>';
                                    return;
                                }

                                data.data.forEach(media => {
                                    const isVideo = media.type.toLowerCase().includes(
                                        'video') || media.type === 'mp4';
                                    mediaContainer.innerHTML += `
                                    <div class="card mb-3 media bg-white" id="media-${media.id}"
                                        media-id="${media.id}"
                                        onmouseover="this.style.backgroundColor='#f0f0f0';"
                                        onmouseout="this.style.backgroundColor='white';">
                                        <div class="shadow-md rounded-lg overflow-hidden">
                                            <div class="p-4 flex flex-col items-center">
                                                <h5 class="text-lg font-bold text-center mb-2">${media.name}</h5>
                                                <p class="text-gray-500 mb-2">${media.type}</p>
                                                <div class="media-preview mb-2">
                                                    ${isVideo ?
                                                        `<video src="${media.media_url}" controls style="max-width: 150px; max-height: 150px;"></video>` :
                                                        `<a href="${media.media_url}" target="_blank">View File</a>`}
                                                </div>
                                                <div class="mt-2">
                                                    <label for="media-${media.id}" class="form-check-label">Select Media</label>
                                                    <input type="checkbox" name="media_ids[]" value="${media.id}"
                                                        class="form-check-switch" id="media-${media.id}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>`;
                                });
                            })
                            .catch(error => {
                                console.error('Error fetching media:', error);
                                mediaContainer.innerHTML = '<p>Error loading media.</p>';
                            });

                        fetch(`/campaign/users-by-enterprise/${enterpriseId}`, {
                                headers: {
                                    'Accept': 'application/json',
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                        .content
                                }
                            })
                            .then(response => response.json())
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
                                userSelect.innerHTML = '<option value="">Error loading users</option>';
                            });
                    } else {
                        mediaContainer.innerHTML = '<p>Select an enterprise.</p>';
                        userSelect.innerHTML = '<option value="">Select a User</option>';
                    }
                });

                enterpriseSelect.dispatchEvent(new Event('change'));
            @endif
        });
    </script>
@endpush
```
