<div class="box p-5 mt-5">
    <div class="p-2">
        <label for="eservice-name" class="form-label">{{ __('eservice-create.name') }}</label>
        <input type="text" name="name" class="form-control form-control-lg" id="eservice-name"
            value="{{ old('name', $row->name ?? request()->input('name')) }}" required>
    </div>

    <div class="p-2">
        <label for="eservice-alias" class="form-label">{{ __('eservice-create.alias') }}</label>
        <input type="text" name="alias" class="form-control form-control-lg" id="eservice-alias"
            value="{{ old('alias', $row->alias ?? request()->input('alias')) }}" readonly required>
    </div>

    <div class="p-2">
        <label for="eservice-description" class="form-label">{{ __('eservice-create.description') }}</label>
        <input type="text" name="description" class="form-control form-control-lg" id="eservice-description"
            value="{{ old('description', $row->description ?? request()->input('description')) }}">
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