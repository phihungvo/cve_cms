<div class="box p-5 mt-5">

    <form id="licenseForm">
        @csrf

        <div class="mb-3">
            <label for="enterprise_id" class="form-label">Enterprise</label>
            <select name="enterprise_id" id="enterprise_id" class="form-select" required>
                <option value="">Select Enterprise</option>
                <!-- Giả lập dữ liệu, thay bằng dữ liệu thực từ database -->
                <option value="1">Enterprise X</option>
                <option value="2">Enterprise Y</option>
                <option value="3">Enterprise Z</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="service_id" class="form-label">Service</label>
            <select name="service_id" id="service_id" class="form-select" required>
                <option value="">Select Service</option>
                <!-- Giả lập dữ liệu, thay bằng dữ liệu thực từ database -->
                <option value="1">Service A</option>
                <option value="2">Service B</option>
                <option value="3">Service C</option>
            </select>
        </div>


        <div class="lg:flex">
            <div class="flex-1 p-2">
                <label for="max_users" class="form-label">Max Users</label>
                <input type="number" name="max_users" id="max_users" class="form-control" required>
            </div>

            <div class="flex-1 p-2">
                <label for="max_devices" class="form-label">Max Devices</label>
                <input type="number" name="max_devices" id="max_devices" class="form-control" required>
            </div>
        </div>

        <div class="lg:flex">
            <div class="flex-1 p-2">
                <label for="start_date" class="form-label">Start Date</label>
                <input type="date" name="start_date" id="start_date" class="form-control" required>
            </div>

            <div class="flex-1 p-2">
                <label for="end_date" class="form-label">End Date</label>
                <input type="date" name="end_date" id="end_date" class="form-control" required>
            </div>
        </div>


        <div class="lg:flex">
            <div class="flex-1 p-2">
                <label for="license_type" class="form-label">{{ __('license-create.license_type') }}</label>
                <select name="license_type" id="license_type" class="form-select" required>
                    <option value="">Select License Type</option>
                    <option value="standard">Standard</option>
                    <option value="premium">Premium</option>
                    <option value="enterprise">Enterprise</option>
                </select>
            </div>

            <div class="flex-1 p-2">
                <label for="status" class="form-label">{{ __('license-create.license_status') }}</label>
                <select name="status" id="status" class="form-select" required>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                    <option value="expired">Expired</option>
                </select>
            </div>
        </div>

        <div class="p-2">
            <label for="license_key" class="form-label">{{ __('license-create.license_key') }}</label>

            <div class="input-group">
                <input type="password" name="api_key" class="form-control form-control-lg" id="license_key"
                    value="{{ $REQUEST->input('api_key') }}" autocomplete="off" />

                <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.show') }}"
                    data-password-show="#license_key" tabindex="-1">@icon('eye', 'w-5 h-5')</button>
                <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.copy') }}"
                    data-copy="#license_key" tabindex="-1">@icon('clipboard', 'w-5 h-5')</button>
                <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.generate') }}"
                    data-password-generate="#license_key" data-password-generate-format="uuid"
                    tabindex="-1">@icon('refresh-cw', 'w-5 h-5')</button>
                <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.reset') }}"
                    data-input-default="#license_key" data-tabindex="-1">@icon('skip-back', 'w-5 h-5')</button>
            </div>
        </div>
    </form>
</div>