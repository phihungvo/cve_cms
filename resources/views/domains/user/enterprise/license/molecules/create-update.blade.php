<div class="box p-5 mt-5">

    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="license-name" class="form-label">{{ __('license-create.name') }}</label>
            <input type="text" name="name" class="form-control form-control-lg" id="license-name"
                value="{{ old('name', $row->name ?? request()->input('name')) }}" required>
        </div>

        <div class="flex-1 p-2">
            <label for="license-alias" class="form-label">{{ __('license-create.alias') }}</label>
            <input type="text" name="alias" class="form-control form-control-lg" id="license-alias"
                value="{{ old('alias', $row->alias ?? request()->input('alias')) }}" readonly required>
        </div>
    </div>

    <div class="p-2">
        <label for="enterprise_id" class="form-label">{{ __('license-create.enterprise') }}</label>
        <select name="enterprise_id" id="enterprise_id" class="form-control form-control-lg" required>
            @foreach($enterpriseOptions as $id => $name)
                <option value="{{ $id }}" {{ old('enterprise_id', $row->enterprise_id ?? request()->input('enterprise_id')) == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="p-2">
        <label for="service_id" class="form-label">{{ __('license-create.service') }}</label>
        <select name="service_id" id="service_id" class="form-control form-control-lg" required>
            @foreach($serviceOptions as $id => $name)
                <option value="{{ $id }}" {{ old('service_id', $row->service_id ?? request()->input('service_id')) == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="max_users" class="form-label">{{ __('license-create.max_user') }}</label>
            <input type="number" name="max_users" id="max_users" class="form-control form-control-lg" min="0"
                value="{{ old('max_users', $row->max_users ?? request()->input('max_users')) }}" required>
        </div>

        <div class="flex-1 p-2">
            <label for="max_devices" class="form-label">{{ __('license-create.max_device') }}</label>
            <input type="number" name="max_devices" id="max_devices" class="form-control form-control-lg" min="0"
                value="{{ old('max_devices', $row->max_devices ?? request()->input('max_devices')) }}" required>
        </div>
    </div>

    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="start_date" class="form-label">{{ __('license-create.start_date') }}</label>
            <input type="date" name="start_date" id="start_date" class="form-control form-control-lg"
                value="{{ old('start_date', isset($row) ? \Carbon\Carbon::parse($row->start_date)->format('Y-m-d') : request()->input('start_date')) }}"
                required>
        </div>

        <div class="flex-1 p-2">
            <label for="end_date" class="form-label">{{ __('license-create.end_date') }}</label>
            <input type="date" name="end_date" id="end_date" class="form-control form-control-lg"
                value="{{ old('end_date', isset($row) ? \Carbon\Carbon::parse($row->end_date)->format('Y-m-d') : request()->input('end_date')) }}"
                required>
        </div>
    </div>

    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="license_type" class="form-label">{{ __('license-create.license_type') }}</label>
            <select name="license_type" id="license_type" class="form-control form-control-lg" required>
                <option value="">Select License Type</option>
                @foreach(['trial', 'standard', 'premium', 'enterprise'] as $type)
                    <option value="{{ $type }}" {{ old('license_type', $row->license_type ?? request()->input('license_type')) == $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 p-2">
            <label for="status" class="form-label">{{ __('license-create.license_status') }}</label>
            <select name="status" id="status" class="form-control form-control-lg" required>
                <option value="">Select License Status</option>
                @foreach(['active', 'expired', 'suspended'] as $status)
                    <option value="{{ $status }}" {{ old('status', $row->status ?? request()->input('status')) == $status ? 'selected' : '' }}>
                        {{ ucfirst($status) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="p-2">
        <label for="license_key" class="form-label">{{ __('license-create.license_key') }}</label>
        <div class="input-group">
            <input type="password" name="license_key" class="form-control form-control-lg" id="license_key"
                value="{{ old('license_key', $row->license_key ?? request()->input('license_key')) }}"
                autocomplete="off" />
            <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.show') }}"
                data-password-show="#license_key" tabindex="-1">@icon('eye', 'w-5 h-5')</button>
            <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.copy') }}"
                data-copy="#license_key" tabindex="-1">@icon('copy', 'w-5 h-5')</button>
            <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.generate') }}"
                data-password-generate="#license_key" data-password-generate-format="uuid"
                tabindex="-1">@icon('refresh-cw', 'w-5 h-5')</button>
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nameInput = document.getElementById('license-name');
            const aliasInput = document.getElementById('license-alias');
            let debounceTimeout;

            // Hàm chuyển đổi tiếng Việt có dấu thành không dấu
            function removeAccents(str) {
                return str.normalize('NFD')
                    .replace(/[\u0300-\u036f]/g, '')
                    .replace(/đ/g, 'd')
                    .replace(/Đ/g, 'D');
            }

            function updateAlias() {
                let value = nameInput.value.trim().toLowerCase();
                value = removeAccents(value);
                value = value.replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, '-');
                aliasInput.value = value;
            }

            // Sự kiện cho alias
            nameInput.addEventListener('input', function () {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(updateAlias, 300);
            });

            // Khởi tạo
            if (nameInput.value) {
                updateAlias();
            }

        });
    </script>
@endpush