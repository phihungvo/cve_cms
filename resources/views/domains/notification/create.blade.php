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

        <form method="POST" action="{{ route('notification.create') }}" class="form" id="notificationForm">
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
                <select name="enterprise_id" id="enterprise_id" class="form-control form-control-lg" {{ auth()->user()->isOwner() ? 'disabled' : '' }}>
                    @if(auth()->user()->isRoot())
                        <option value="">{{ __('notification-create.enterprise-none') }}</option>
                        @foreach(\App\Domains\User\Enterprise\Model\Enterprise::all() as $enterprise)
                            <option value="{{ $enterprise->id }}" {{ old('enterprise_id') == $enterprise->id ? 'selected' : '' }}>
                                {{ $enterprise->name }}
                            </option>
                        @endforeach
                    @else
                        <option value="{{ auth()->user()->enterprise_id }}" selected>
                            {{ \App\Domains\User\Enterprise\Model\Enterprise::find(auth()->user()->enterprise_id)?->name }}
                        </option>
                    @endif
                </select>

                @if(auth()->user()->isOwner())
                    <input type="hidden" name="enterprise_id" value="{{ auth()->user()->enterprise_id }}">
                @endif
            </div>

            <!-- Nhóm mục tiêu -->
            <div class="mb-4">
                <label for="target_group" class="form-label">{{ __('notification-create.target-group-label') }}</label>
                <select name="target_group" id="target_group" class="form-control form-control-lg select2">
                    <option value="">{{ __('notification-create.target-group-none') }}</option>
                    <option value="all">{{ __('notification-create.target-group-all') }}</option>
                    <!-- Danh sách role sẽ được tải động qua AJAX -->
                </select>

            </div>

            <!-- Người dùng cụ thể -->
            <div class="mb-4">
                <label for="user_ids" class="form-label">{{ __('notification-create.user-ids-label') }}</label>
                <select name="user_ids[]" id="user_ids" multiple class="form-control form-control-lg select2">
                    <option value="">{{ __('notification-create.user-ids-none') }}</option>
                    <!-- Danh sách user sẽ được tải động qua AJAX -->
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

@push('scripts')
    <script>
        $(document).ready(function () {
            // Khởi tạo Select2 cho user_ids và target_group
            $('#user_ids').select2({
                placeholder: "{{ __('notification-create.user-ids-none') }}",
                allowClear: true
            });

            $('#target_group').select2({
                placeholder: "{{ __('notification-create.target-group-none') }}",
                allowClear: true
            });

            // Hàm tải danh sách user theo enterprise_id
            function loadUsers(enterpriseId) {
                if (!enterpriseId) {
                    $('#user_ids').empty().append(
                        $('<option>', {
                            value: '',
                            text: '{{ __('notification-create.user-ids-none') }}'
                        })
                    ).trigger('change');
                    return;
                }

                $.ajax({
                    url: '{{ route("notification.users-by-enterprise") }}',
                    type: 'GET',
                    data: { enterprise_id: enterpriseId },
                    success: function (response) {
                        $('#user_ids').empty().append(
                            $('<option>', {
                                value: '',
                                text: '{{ __('notification-create.user-ids-none') }}'
                            })
                        ); // Thêm tùy chọn mặc định
                        if (response.users.length > 0) {
                            $.each(response.users, function (index, user) {
                                $('#user_ids').append(
                                    $('<option>', {
                                        value: user.id,
                                        text: user.name + ' (' + user.email + ')'
                                    })
                                );
                            });
                        } else {
                            $('#user_ids').append(
                                $('<option>', {
                                    value: '',
                                    text: 'Không có người dùng nào'
                                })
                            );
                        }
                        $('#user_ids').trigger('change'); // Cập nhật Select2
                    },
                    error: function () {
                        alert('Không thể tải danh sách người dùng.');
                    }
                });
            }

            // Hàm tải danh sách role theo enterprise_id
            function loadRoles(enterpriseId) {
                if (!enterpriseId) {
                    $('#target_group').empty().append(
                        $('<option>', {
                            value: '',
                            text: '{{ __('notification-create.target-group-none') }}'
                        })
                    ).trigger('change');
                    return;
                }

                $.ajax({
                    url: '{{ route("notification.roles-by-enterprise") }}',
                    type: 'GET',
                    data: { enterprise_id: enterpriseId },
                    success: function (response) {
                        $('#target_group').empty().append(
                            $('<option>', {
                                value: '',
                                text: '{{ __('notification-create.target-group-none') }}'
                            })
                        ).append(
                            $('<option>', {
                                value: 'all',
                                text: '{{ __('notification-create.target-group-all') }}'
                            })
                        ); // Thêm tùy chọn mặc định và "All"
                        if (response.roles.length > 0) {
                            $.each(response.roles, function (index, role) {
                                $('#target_group').append(
                                    $('<option>', {
                                        value: role.name,
                                        text: role.name
                                    })
                                );
                            });
                        }
                        $('#target_group').trigger('change'); // Cập nhật Select2
                    },
                    error: function () {
                        alert('Không thể tải danh sách vai trò.');
                    }
                });
            }

            // Khi enterprise_id thay đổi
            $('#enterprise_id').on('change', function () {
                var enterpriseId = $(this).val();
                loadUsers(enterpriseId);
                loadRoles(enterpriseId);
            });

            // Tải danh sách user và role ban đầu (nếu enterprise_id đã được chọn)
            @if(old('enterprise_id') || auth()->user()->isOwner())
                var initialEnterpriseId = {{ old('enterprise_id', auth()->user()->isOwner() ? auth()->user()->enterprise_id : 'null') }};
                loadUsers(initialEnterpriseId);
                loadRoles(initialEnterpriseId);
            @endif
                });
    </script>
@endpush