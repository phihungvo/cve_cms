<div class="box p-5 mt-5">
    <div class="p-2">
        <label for="feature-name" class="form-label">{{ __('role-feature-create.name') }}</label>
        <input type="text" name="name" class="form-control form-control-lg" id="feature-name"
            value="{{ old('name', $row->name ?? request()->input('name')) }}" required>

    </div>

    <div class="p-2">
        <label for="feature-alias" class="form-label">{{ __('role-feature-create.alias') }}</label>
        <input type="text" name="alias" class="form-control form-control-lg" id="feature-alias"
            value="{{ old('alias', $row->alias ?? request()->input('alias')) }}" readonly required>

    </div>

    <div class="p-2">
        <label for="feature-description" class="form-label">{{ __('role-feature-create.description') }}</label>
        <input type="text" name="description" class="form-control form-control-lg" id="feature-description"
            value="{{ old('description', $row->description ?? request()->input('description')) }}">

    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nameInput = document.getElementById('feature-name');
            const aliasInput = document.getElementById('feature-alias');
            let debounceTimeout;

            function updateAlias() {
                let value = nameInput.value.trim().toLowerCase();
                value = value.replace(/[^a-z0-9\s]/g, '').replace(/\s+/g, '-');
                aliasInput.value = value;
            }

            nameInput.addEventListener('input', function () {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(updateAlias, 300);
            });

            if (nameInput.value) {
                updateAlias();
            }
        });
    </script>
@endpush