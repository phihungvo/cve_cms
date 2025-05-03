<div class="box p-5 mt-5">
    <div class="mb-3">
        <label class="form-label">{{ __('license-create.enterprise') }}</label>
        <select name="enterprise_id" class="form-select" required>
            @foreach($enterpriseOptions as $id => $name)
                <option value="{{ $id }}" {{ old('enterperise_id') == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="mb-3">
        <label class="form-label">{{ __('license-create.service') }}</label>
        <select name="service_id" class="form-select" required>
            @foreach($serviceOptions as $id => $name)
                <option value="{{ $id }}" {{ old('enterperise_id') == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="max_users" class="form-label">{{ __('license-create.max_user') }}</label>
            <input type="number" name="max_users" id="max_users" class="form-control" min="0" required>
        </div>

        <div class="flex-1 p-2">
            <label for="max_devices" class="form-label">{{ __('license-create.max_device') }}</label>
            <input type="number" name="max_devices" id="max_devices" class="form-control" min="0" required>
        </div>
    </div>

    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="start_date" class="form-label">{{ __('license-create.start_date') }}</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
        </div>

        <div class="flex-1 p-2">
            <label for="end_date" class="form-label">{{ __('license-create.end_date') }}</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required>
        </div>
    </div>


    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="license_type" class="form-label">{{ __('license-create.license_type') }}</label>
            <select name="license_type" id="license_type" class="form-select" required>
                <option value="">Select License Type</option>
                <option value="trial">Trial</option>
                <option value="standard">Standard</option>
                <option value="premium">Premium</option>
                <option value="enterprise">Enterprise</option>
            </select>
        </div>

        <div class="flex-1 p-2">
            <label for="status" class="form-label">{{ __('license-create.license_status') }}</label>
            <select name="status" id="status" class="form-select" required>
                <option value="">Select License Status</option>
                <option value="active">Active</option>
                <option value="expired">Expired</option>
                <option value="suspended">Suspended</option>
            </select>
        </div>
    </div>

    <div class="p-2">
        <label for="license_key" class="form-label">{{ __('license-create.license_key') }}</label>

        <div class="input-group">
            <input type="password" name="license_key" class="form-control form-control-lg" id="license_key"
                value="{{ $REQUEST->input('license_key') }}" autocomplete="off" />

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