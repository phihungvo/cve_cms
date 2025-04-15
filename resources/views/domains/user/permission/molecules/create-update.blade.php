<div class="box p-5 mt-5">
    <div class="p-2">
        <label for="permission-name" class="form-label">{{ __('permission-create.name') }}</label>
        <input type="text" name="name" class="form-control form-control-lg" id="permission-name"
            value="{{ old('name', $row->name ?? request()->input('name')) }}" required>
    </div>

    <div class="p-2">
        <label for="permission-alias" class="form-label">{{ __('permission-create.alias') }}</label>
        <input type="text" name="alias" class="form-control form-control-lg" id="permission-alias"
            value="{{ old('alias', $row->alias ?? request()->input('alias')) }}" readonly required>
    </div>

    <div class="p-2">
        <label for="permission-description" class="form-label">{{ __('permission-create.description') }}</label>
        <input type="text" name="description" class="form-control form-control-lg" id="permission-description"
            value="{{ old('description', $row->description ?? request()->input('description')) }}">
    </div>



        <!-- Trường parent_id (luôn hiển thị) -->
    <div class="p-2">
         <label for="permission-parent-id" class="form-label">{{ __('permission-create.parent_id') }}</label>
        <select name="parent_id" class="form-control form-control-lg" id="permission-parent-id">
            <option value="0">{{ __('permission-create.no_parent') }}</option>
            @foreach($permissions as $permission)
                <option value="{{ $permission->id }}"
                    {{ old('parent_id', $row->parent_id ?? '') == $permission->id ? 'selected' : '' }}>
                    {{ $permission->name }}
                </option>
            @endforeach
        </select>
    </div>

        <!-- Trường menu_route (luôn hiển thị) -->
    <div class="p-2">
        <label for="permission-menu-route" class="form-label">{{ __('permission-create.menu_route') }}</label>
        <input type="text" name="menu_route" class="form-control form-control-lg" id="permission-menu-route"
            value="{{ old('menu_route', $row->menu_route ?? request()->input('menu_route')) }}"
            {{ old('is_menu', $row->is_menu ?? 0) ? 'required' : '' }}>
    </div>

    <!-- Trường is_menu (toggle) -->
    <div class="p-2">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_menu" id="permission-is-menu"
                value="1" {{ old('is_menu', $row->is_menu ?? 0) ? 'checked' : '' }}>
            <label class="form-check-label" for="permission-is-menu">{{ __('permission-create.is_menu') }}</label>
        </div>
    </div>


    <!-- Các trường chỉ hiển thị khi is_menu được bật -->
    <div class="p-2 menu-dependent" style="display: none;">
        <label for="permission-menu-name" class="form-label">{{ __('permission-create.menu_name') }}</label>
        <input type="text" name="menu_name" class="form-control form-control-lg" id="permission-menu-name"
            value="{{ old('menu_name', $row->menu_name ?? request()->input('menu_name')) }}">
    </div>

    <div class="p-2 menu-dependent" style="display: none;">
        <label for="permission-menu-icon" class="form-label">{{ __('permission-create.menu_icon') }}</label>
        <input type="text" name="menu_icon" class="form-control form-control-lg" id="permission-menu-icon"
            value="{{ old('menu_icon', $row->menu_icon ?? request()->input('menu_icon')) }}">
    </div>

</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nameInput = document.getElementById('permission-name');
            const aliasInput = document.getElementById('permission-alias');
            const isMenuInput = document.getElementById('permission-is-menu');
            const menuRouteInput = document.getElementById('permission-menu-route');
            const menuDependentFields = document.querySelectorAll('.menu-dependent');
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

            function toggleMenuFields() {
                const isChecked = isMenuInput.checked;
                menuDependentFields.forEach(field => {
                    field.style.display = isChecked ? 'block' : 'none';
                });
                menuRouteInput.required = isChecked;
            }

            // Sự kiện cho alias
            nameInput.addEventListener('input', function () {
                clearTimeout(debounceTimeout);
                debounceTimeout = setTimeout(updateAlias, 300);
            });

            // Sự kiện cho is_menu toggle
            isMenuInput.addEventListener('change', toggleMenuFields);

            // Khởi tạo
            if (nameInput.value) {
                updateAlias();
            }
            toggleMenuFields(); // Hiển thị/khóa dựa trên giá trị ban đầu
        });
    </script>
@endpush