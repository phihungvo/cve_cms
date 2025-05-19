<div class="box p-5 mt-5">
    <!-- Debugging output (remove in production) -->
    @if(app()->environment('local'))
        <div class="p-2">
            <pre>Licenses: {{ print_r($licenses ?? [], true) }}</pre>
            <pre>Row: {{ print_r($row ?? [], true) }}</pre>
            @php
                \Illuminate\Support\Facades\Log::debug('Data in billing view', [
                    'licenses' => $licenses ?? [],
                    'license_count' => count($licenses ?? []),
                    'row' => $row ?? null
                ]);
            @endphp
        </div>
    @endif

    <div class="p-2">
        <label for="name" class="form-label">{{ __('billing.name') }}</label>
        <input type="text" name="name" class="form-control form-control-lg" id="name"
            value="{{ old('name', isset($row) ? $row->name : '') }}" required>
    </div>

    <div class="p-2">
        <label for="license_id" class="form-label">{{ __('billing.license') }}</label>
        <select name="license_id" id="license_id" class="form-control form-control-lg"
            onchange="populateLicenseDetails(this)">
            <option value="">{{ __('billing.select_license') }}</option>
            @foreach($licenses ?? [] as $license)
                <option value="{{ $license['id'] }}" data-license='{{ json_encode($license) }}' {{ old('license_id', isset($row) ? $row->license_id : '') == $license['id'] ? 'selected' : '' }}>
                    {{ $license['name'] ?? $license['license_key'] ?? 'Billing #' . $license['id'] }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="p-2">
        <label for="enterprise_id" class="form-label">{{ __('billing.enterprise') }}</label>
        <input type="text" id="enterprise_id" class="form-control form-control-lg"
            value="{{ old('enterprise_id', isset($row) && $row->license && $row->license->enterprise ? $row->license->enterprise->name : '') }}"
            readonly>
    </div>

    <div class="p-2">
        <label for="license_key" class="form-label">{{ __('billing.license_key') }}</label>
        <div class="input-group">
            <input type="password" id="license_key" class="form-control form-control-lg"
                value="{{ old('license_key', isset($row) && $row->license ? $row->license->license_key : '') }}"
                readonly>
            <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.show') }}"
                data-password-show="#license_key" tabindex="-1">@icon('eye', 'w-5 h-5')</button>
            <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.copy') }}"
                data-copy="#license_key" tabindex="-1">@icon('copy', 'w-5 h-5')</button>
        </div>
    </div>

    <div class="p-2">
        <label for="service_id" class="form-label">{{ __('billing.service') }}</label>
        <input type="text" id="service_id" class="form-control form-control-lg"
            value="{{ old('service_id', isset($row) && $row->license && $row->license->service ? ($row->license->service->name ?? $row->license->service->alias ?? '') : '') }}"
            readonly>
    </div>

    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="license_type" class="form-label">{{ __('billing.license_type') }}</label>
            <input type="text" id="license_type" class="form-control form-control-lg"
                value="{{ old('license_type', isset($row) && $row->license ? $row->license->license_type : '') }}"
                readonly>
        </div>

        <div class="flex-1 p-2">
            <label for="status" class="form-label">{{ __('billing.license_status') }}</label>
            <input type="text" id="status" class="form-control form-control-lg"
                value="{{ old('status', isset($row) && $row->license ? $row->license->status : '') }}" readonly>
        </div>
    </div>

    <div class="p-2">
        <label for="max_unit" class="form-label">{{ __('billing.max_unit') }}</label>
        <input type="text" id="max_unit" class="form-control form-control-lg"
            value="{{ old('max_unit', isset($row) && $row->license && $row->license->service ? ($row->license->service->max_unit ?? 'N/A') : 'N/A') }}"
            readonly>
    </div>

    <div class="p-2">
        <label for="price" class="form-label">{{ __('billing.price') }}</label>
        <input type="text" id="price" class="form-control form-control-lg"
            value="{{ old('price', isset($row) && $row->license && $row->license->service && ($row->license->service->price ?? 0) > 0 ? ($row->license->service->price . ' / ' . ($row->license->service->billing_cycle ?? 'monthly')) : 'N/A') }}"
            readonly>
    </div>

    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="start_date" class="form-label">{{ __('billing.start_date') }}</label>
            <input type="date" name="start_date" id="start_date" class="form-control form-control-lg"
                value="{{ old('start_date', isset($row) && $row->start_date ? \Carbon\Carbon::parse($row->start_date)->format('Y-m-d') : '') }}"
                required>
        </div>

        <div class="flex-1 p-2">
            <label for="end_date" class="form-label">{{ __('billing.end_date') }}</label>
            <input type="date" name="end_date" id="end_date" class="form-control form-control-lg"
                value="{{ old('end_date', isset($row) && $row->end_date ? \Carbon\Carbon::parse($row->end_date)->format('Y-m-d') : '') }}"
                required>
        </div>

        <div class="flex-1 p-2">
            <label for="duration" class="form-label">{{ __('billing.duration') }}</label>
            <input type="text" id="duration" class="form-control form-control-lg" value="" readonly>
        </div>
    </div>

    <div class="p-2">
        <label for="usage_unit" class="form-label">{{ __('billing.usage_unit') }}</label>
        <input type="number" name="usage_unit" id="usage_unit" class="form-control form-control-lg" min="0"
            value="{{ old('usage_unit', isset($row) ? $row->usage_unit : '') }}" required>
        <div id="usage_unit_error" class="text-red-500 text-sm mt-1 hidden">{{ __('billing.usage_unit_exceeds_max') }}
        </div>
    </div>

    <div class="p-2">
        <label for="total_price" class="form-label">{{ __('billing.total_price') }}</label>
        <input type="text" id="total_price" class="form-control form-control-lg" value="" readonly>
        <input type="hidden" name="price" id="total_price_value"
            value="{{ old('price', isset($row) ? $row->price : '') }}">
    </div>

    <div class="p-2">
        <label for="payment_status" class="form-label">{{ __('billing.payment_status') }}</label>
        <select name="payment_status" id="payment_status" class="form-control form-control-lg" required>
            <option value="">{{ __('billing.select_payment_status') }}</option>
            @foreach(['pending', 'paid', 'failed'] as $payment_status)
                <option value="{{ $payment_status }}" {{ old('payment_status', isset($row) ? $row->payment_status : '') == $payment_status ? 'selected' : '' }}>
                    {{ ucfirst($payment_status) }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<script>
    function populateLicenseDetails(select) {
        console.log('Populating license details for selected license');
        let licenseData;
        try {
            const selectedOption = select.options[select.selectedIndex];
            licenseData = selectedOption.getAttribute('data-license') ? JSON.parse(selectedOption.getAttribute('data-license')) : {};
            console.log('Selected license data:', licenseData);
        } catch (error) {
            console.error('Error parsing license data:', error, select.options[select.selectedIndex].getAttribute('data-license'));
            licenseData = {};
        }

        // Update enterprise fields
        const enterprise = licenseData.enterprise || {};
        document.getElementById('enterprise_id').value = typeof enterprise.name === 'string' ? enterprise.name : '';

        // Update service fields
        const service = licenseData.service || {};
        document.getElementById('service_id').value = typeof service.name === 'string' ? service.name : (typeof service.alias === 'string' ? service.alias : '');

        // Update max_unit field
        const maxUnit = typeof service.max_unit === 'number' ? service.max_unit : 'N/A';
        document.getElementById('max_unit').value = maxUnit;

        // Update price field
        const price = typeof service.price === 'number' && service.price > 0 ? `${service.price.toFixed(2)} / ${service.billing_cycle || 'monthly'}` : 'N/A';
        document.getElementById('price').value = price;

        // Update license fields
        document.getElementById('license_type').value = typeof licenseData.license_type === 'string' ? licenseData.license_type : '';
        document.getElementById('status').value = typeof licenseData.status === 'string' ? licenseData.status : '';
        document.getElementById('license_key').value = typeof licenseData.license_key === 'string' ? licenseData.license_key : '';

        // Calculate duration and prices
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const usageUnitInput = document.getElementById('usage_unit');
        const usageUnitError = document.getElementById('usage_unit_error');

        function updateDurationAndPrices() {
            console.log('Updating duration and prices', {
                start_date: startDateInput.value,
                end_date: endDateInput.value,
                usage_unit: usageUnitInput.value
            });

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
            const servicePrice = typeof service.price === 'number' ? service.price : 0;
            const totalPrice = servicePrice > 0 && duration > 0 && usageUnit > 0
                ? (servicePrice * usageUnit * duration).toFixed(2)
                : 'N/A';
            document.getElementById('total_price').value = totalPrice;
            document.getElementById('total_price_value').value = totalPrice !== 'N/A' ? Math.round(parseFloat(totalPrice)) : '';

            console.log('Calculated values:', {
                duration: durationText,
                price: document.getElementById('price').value,
                total_price: totalPrice,
                submitted_price: document.getElementById('total_price_value').value
            });
        }

        // Initial calculation
        updateDurationAndPrices();

        // Validate usage_unit on input
        usageUnitInput.addEventListener('input', function () {
            const usageUnit = parseFloat(this.value);
            const maxUnitValue = typeof service.max_unit === 'number' ? service.max_unit : Infinity;
            if (usageUnit > maxUnitValue) {
                usageUnitError.classList.remove('hidden');
                this.value = maxUnitValue; // Cap the value
            } else {
                usageUnitError.classList.add('hidden');
            }
            console.log('Usage unit updated:', { usage_unit: this.value, max_unit: maxUnitValue });
            updateDurationAndPrices();
        });

        // Update duration and prices on date change
        startDateInput.addEventListener('input', () => {
            console.log('Start date changed:', startDateInput.value);
            updateDurationAndPrices();
        });
        endDateInput.addEventListener('input', () => {
            console.log('End date changed:', endDateInput.value);
            updateDurationAndPrices();
        });
    }

    // Auto-populate fields when updating
    document.addEventListener('DOMContentLoaded', function () {
        const licenseSelect = document.getElementById('license_id');
        if (licenseSelect && licenseSelect.value) {
            console.log('Auto-populating license details for update');
            populateLicenseDetails(licenseSelect);
        }
    });
</script>