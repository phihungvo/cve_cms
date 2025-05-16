<div class="box p-5 mt-5">
    <div class="p-2">
        <label for="enterprise_id" class="form-label">{{ __('eservice-create.enterprise') }}</label>
        <select name="enterprise_id" id="enterprise_id" class="form-control form-control-lg" required>
            @foreach($enterpriseOptions as $id => $name)
                <option value="{{ $id }}" {{ old('enterprise_id', $row->enterprise_id ?? request()->input('enterprise_id')) == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="eservice-name" class="form-label">{{ __('eservice-create.name') }}</label>
            <input type="text" name="name" class="form-control form-control-lg" id="eservice-name"
                value="{{ old('name', $row->name ?? request()->input('name')) }}" required>
        </div>

        <div class="flex-1 p-2">
            <label for="eservice-alias" class="form-label">{{ __('eservice-create.alias') }}</label>
            <input type="text" name="alias" class="form-control form-control-lg" id="eservice-alias"
                value="{{ old('alias', $row->alias ?? request()->input('alias')) }}" readonly required>
        </div>
    </div>



    <div class="p-2">
        <label for="eservice-description" class="form-label">{{ __('eservice-create.description') }}</label>
        <input type="text" name="description" class="form-control form-control-lg" id="eservice-description"
            value="{{ old('description', $row->description ?? request()->input('description')) }}">
    </div>


    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="pricing_model" class="form-label">{{ __('eservice-create.pricing_model') }}</label>
            <select name="pricing_model" id="pricing_model" class="form-control form-control-lg" required>
                <option value="">Select Pricing Model</option>
                @foreach(['fixed', 'per_unit'] as $type)
                    <option value="{{ $type }}" {{ old('pricing_model', $row->pricing_model ?? request()->input('pricing_model')) == $type ? 'selected' : '' }}>
                        {{ __("eservice-create.$type") }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="flex-1 p-2">
            <label for="billing_cycle" class="form-label">{{ __('eservice-create.billing_cycle') }}</label>
            <select name="billing_cycle" id="billing_cycle" class="form-control form-control-lg" required>
                <option value="">Select Billing Cycle</option>
                @foreach(['monthly', 'yearly'] as $type)
                    <option value="{{ $type }}" {{ old('billing_cycle', $row->billing_cycle ?? request()->input('billing_cycle')) == $type ? 'selected' : '' }}>
                        {{ ucfirst($type) }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="lg:flex">
        <div class="flex-1 p-2">
            <label for="max_unit" class="form-label">{{ __('eservice-create.max_unit') }}</label>
            <input type="number" name="max_unit" id="max_unit" class="form-control form-control-lg" min="0"
                value="{{ old('max_unit', $row->max_unit ?? request()->input('max_unit')) }}" required>
        </div>

        <div class="flex-1 p-2">
            <label for="price" class="form-label">{{ __('eservice-create.price') }}</label>
            <input type="number" name="price" id="price" class="form-control form-control-lg" min="0" step="0.01"
                value="{{ old('price', number_format((float) ($row->price ?? request()->input('price', 0)), 2, '.', '')) }}"
                required>
        </div>

    </div>

    <div class="p-2">
        <label for="eservice-note" class="form-label">{{ __('eservice-create.note') }}</label>
        <input type="text" name="note" class="form-control form-control-lg" id="eservice-note"
            value="{{ old('note', $row->note ?? request()->input('note')) }}">
    </div>

</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nameInput = document.getElementById('eservice-name');
            const aliasInput = document.getElementById('eservice-alias');
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