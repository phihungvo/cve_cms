<div class="box p-5 mt-5">
    <!-- Debugging output (remove in production) -->
    @if(app()->environment('local'))
        <div class="p-2">
            <pre>Licenses: {{ print_r($licenses ?? [], true) }}</pre>
        </div>
    @endif

    <div class="p-2">
        <label for="license_id" class="form-label">{{ __('billing.license') }}</label>
        <select name="license_id" id="license_id" class="form-control form-control-lg" onchange="populateLicenseDetails(this)">
            <option value="">{{ __('billing.select_license') }}</option>
            @foreach($licenses ?? [] as $license)
                <option value="{{ $license['id'] }}"
                        data-license='{{ json_encode($license) }}'
                        {{ old('license_id') == $license['id'] ? 'selected' : '' }}>
                    {{ $license['name'] ?? $license['license_key'] ?? 'Billing #' . $license['id'] }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="p-2">
        <label for="enterprise_id" class="form-label">{{ __('billing.enterprise') }}</label>
        <input type="text" id="enterprise_id" class="form-control form-control-lg" value="{{ is_array(old('enterprise_id')) ? '' : old('enterprise_id', '') }}" readonly>
        <input type="hidden" name="enterprise_id" id="enterprise_id_value" value="{{ is_array(old('enterprise_id')) ? '' : old('enterprise_id', '') }}">
    </div>


        <div class="p-2">
        
            <label for="license_key" class="form-label">{{ __('billing.license_key') }}</label>
            <div class="input-group">
                <input type="password" id="license_key" class="form-control form-control-lg"
                    value="{{ is_array(old('license_key')) ? '' : old('license_key', '') }}" readonly>
                <input type="hidden" name="license_key" id="license_key_value"
                    value="{{ is_array(old('license_key')) ? '' : old('license_key', '') }}">
                <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.show') }}"
                    data-password-show="#license_key" tabindex="-1">@icon('eye', 'w-5 h-5')</button>
                <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.copy') }}"
                    data-copy="#license_key" tabindex="-1">@icon('copy', 'w-5 h-5')</button>
        
            </div>
        </div>

    <div class="p-2">
        <label for="service_id" class="form-label">{{ __('billing.service') }}</label>
        <input type="text" id="service_id" class="form-control form-control-lg" value="{{ is_array(old('service_id')) ? '' : old('service_id', '') }}" readonly>
        <input type="hidden" name="service_id" id="service_id_value" value="{{ is_array(old('service_id')) ? '' : old('service_id', '') }}">
    </div>

        <div class="lg:flex">
            <div class="flex-1 p-2">
                <label for="license_type" class="form-label">{{ __('billing.license_type') }}</label>
                <input type="text" id="license_type" class="form-control form-control-lg"
                    value="{{ is_array(old('license_type')) ? '' : old('license_type', '') }}" readonly>
                <input type="hidden" name="license_type" id="license_type_value"
                    value="{{ is_array(old('license_type')) ? '' : old('license_type', '') }}">
            </div>
        
            <div class="flex-1 p-2">
                <label for="status" class="form-label">{{ __('billing.license_status') }}</label>
                <input type="text" id="status" class="form-control form-control-lg"
                    value="{{ is_array(old('status')) ? '' : old('status', '') }}" readonly>
                <input type="hidden" name="status" id="status_value"
                    value="{{ is_array(old('status')) ? '' : old('status', '') }}">
            </div>
        </div>

    <div class="p-2">
        <label for="max_unit" class="form-label">{{ __('billing.max_unit') }}</label>
        <input type="text" id="max_unit" class="form-control form-control-lg" value="" readonly>
    </div>

        <div class="p-2">
            <label for="price" class="form-label">{{ __('billing.price') }}</label>
            <input type="text" id="price" class="form-control form-control-lg" value="" readonly>
        </div>

    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="start_date" class="form-label">{{ __('billing.start_date') }}</label>
            <input type="date" name="start_date" id="start_date" class="form-control form-control-lg" value="{{ is_array(old('start_date')) ? '' : old('start_date', '') }}" required>
        </div>

        <div class="flex-1 p-2">
            <label for="end_date" class="form-label">{{ __('billing.end_date') }}</label>
            <input type="date" name="end_date" id="end_date" class="form-control form-control-lg" value="{{ is_array(old('end_date')) ? '' : old('end_date', '') }}" required>
        </div>

            <div class=" flex-1 p-2">
                <label for="duration" class="form-label">{{ __('billing.duration') }}</label>
                <input type="text" id="duration" class="form-control form-control-lg" value="" readonly>
            </div>
    </div>

    <div class="p-2">
        <label for="usage_unit" class="form-label">{{ __('billing.usage_unit') }}</label>
        <input type="number" name="usage_unit" id="usage_unit" class="form-control form-control-lg" min="0"
            value="{{ is_array(old('usage_unit')) ? '' : old('usage_unit', '') }}" required>
        <div id="usage_unit_error" class="text-red-500 text-sm mt-1 hidden">{{ __('billing.usage_unit_exceeds_max') }}</div>
    </div>

    <div class="p-2">
        <label for="total_price" class="form-label">{{ __('billing.total_price') }}</label>
        <input type="text" id="total_price" class="form-control form-control-lg" value="" readonly>
    </div>



    <div class="p-2">
        <label for="payment_status" class="form-label">{{ __('billing.payment_status') }}</label>
        <select name="payment_status" id="payment_status" class="form-control form-control-lg" required>
            <option value="">{{ __('billing.select_payment_status') }}</option>
            @foreach(['pending', 'paid', 'failed'] as $payment_status)
                <option value="{{ $payment_status }}" {{ old('payment_status') == $payment_status ? 'selected' : '' }}>
                    {{ ucfirst($payment_status) }}
                </option>
            @endforeach
        </select>
    </div>


</div>

<script>
function populateLicenseDetails(select) {
    const selectedOption = select.options[select.selectedIndex];
    const licenseData = selectedOption.getAttribute('data-license') ? JSON.parse(selectedOption.getAttribute('data-license')) : {};

    // Update enterprise fields
    const enterprise = licenseData.enterprise || {};
    document.getElementById('enterprise_id').value = typeof enterprise.name === 'string' ? enterprise.name : '';
    document.getElementById('enterprise_id_value').value = typeof enterprise.id === 'number' ? enterprise.id : '';

    // Update service fields
    const service = licenseData.service || {};
    document.getElementById('service_id').value = typeof service.name === 'string' ? service.name : (typeof service.alias === 'string' ? service.alias : '');
    document.getElementById('service_id_value').value = typeof service.id === 'number' ? service.id : '';

    // Update max_unit field
    const maxUnit = typeof service.max_unit === 'number' ? service.max_unit : 'N/A';
    document.getElementById('max_unit').value = maxUnit;

    // Update usage_unit field
    const usageUnitInput = document.getElementById('usage_unit');
    const usageUnitError = document.getElementById('usage_unit_error');
    usageUnitInput.value = typeof licenseData.usage_unit === 'number' ? licenseData.usage_unit : '';

    // Update date fields
    const startDateInput = document.getElementById('start_date');
    const endDateInput = document.getElementById('end_date');
    startDateInput.value = typeof licenseData.start_date === 'string' ? licenseData.start_date.split('T')[0] : '';
    endDateInput.value = typeof licenseData.end_date === 'string' ? licenseData.end_date.split('T')[0] : '';

    // Update license fields
    document.getElementById('license_type').value = typeof licenseData.license_type === 'string' ? licenseData.license_type : '';
    document.getElementById('license_type_value').value = typeof licenseData.license_type === 'string' ? licenseData.license_type : '';
    document.getElementById('status').value = typeof licenseData.status === 'string' ? licenseData.status : '';
    document.getElementById('status_value').value = typeof licenseData.status === 'string' ? licenseData.status : '';
    document.getElementById('license_key').value = typeof licenseData.license_key === 'string' ? licenseData.license_key : '';
    document.getElementById('license_key_value').value = typeof licenseData.license_key === 'string' ? licenseData.license_key : '';

    // Update payment_status field
    document.getElementById('payment_status').value = typeof licenseData.payment_status === 'string' ? licenseData.payment_status : '';

    // Calculate duration and prices
    function updateDurationAndPrices() {
        const startDate = new Date(startDateInput.value);
        const endDate = new Date(endDateInput.value);
        const billingCycle = typeof service.billing_cycle === 'string' ? service.billing_cycle : 'monthly';
        let duration = 0;
        let durationText = '';

        if (startDate && endDate && !isNaN(startDate) && !isNaN(endDate) && endDate >= startDate) {
            const diffTime = endDate - startDate;
            const diffDays = diffTime / (1000 * 60 * 60 * 24);
            if (billingCycle === 'monthly') {
                duration = Math.ceil(diffDays / 30.42); // Round up to nearest month
                durationText = duration === 1 ? `${duration} month` : `${duration} months`;
            } else if (billingCycle === 'yearly') {
                duration = Math.ceil(diffDays / 365.25); // Round up to nearest year
                durationText = duration === 1 ? `${duration} year` : `${duration} years`;
            }
        } else {
            durationText = 'N/A';
        }
        document.getElementById('duration').value = durationText;

        const usageUnit = parseFloat(usageUnitInput.value) || 0;
        const price = typeof service.price === 'number' ? service.price : 0;
        const priceText = price > 0 ? `${price.toFixed(2)} / ${billingCycle}` : 'N/A';
        document.getElementById('price').value = priceText;

        const totalPrice = price > 0 && duration > 0 && usageUnit > 0
            ? (price * usageUnit * duration).toFixed(2)
            : 'N/A';
        document.getElementById('total_price').value = totalPrice;
    }

    // Initial calculation
    updateDurationAndPrices();

    // Validate usage_unit on input
    usageUnitInput.addEventListener('input', function() {
        const usageUnit = parseFloat(this.value);
        const maxUnitValue = typeof service.max_unit === 'number' ? service.max_unit : Infinity;
        if (usageUnit > maxUnitValue) {
            usageUnitError.classList.remove('hidden');
            this.value = maxUnitValue; // Cap the value
        } else {
            usageUnitError.classList.add('hidden');
        }
        updateDurationAndPrices();
    });

    // Update duration and prices on date change
    startDateInput.addEventListener('input', updateDurationAndPrices);
    endDateInput.addEventListener('input', updateDurationAndPrices);
}
</script>