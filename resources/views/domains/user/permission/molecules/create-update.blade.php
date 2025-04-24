<div class="box p-5 mt-5">
    <div class="p-2">
        <label for="permission-name" class="form-label">{{ __('permission-create.name') }}</label>
        <input type="text" name="name" class="form-control form-control-lg" id="permission-name"
            value="{{ old('name', $row->name ?? request()->input('name')) }}" required>
        <small style="display: block; margin-top: 0.5rem; line-height: 1.5;">
            <ul style="margin: 0; padding-left: 1.2rem;">
                <li>Bắt buộc nhập.</li>
                <li>Bắt đầu bằng "Access".</li>
                <li>Kết thúc bằng một trong các từ sau:</li>
                <li>- Any: Bất kỳ phương thức</li>
                <li>- List: Method GET</li>
                <li>- Update: Method PATCH</li>
                <li>- Delete: Method DELETE</li>
                <li>- Create: Method POST</li>
                <li>- Restore: Method POST, PATCH</li>
                <li>- Show: Method GET</li>
                <li>Chỉ dùng chữ, số, dấu cách, gạch ngang, chấm.</li>
                <li>Ví dụ: Access User List.</li>
            </ul>
        </small>
    </div>

    <div class="p-2">
        <label for="permission-alias" class="form-label">{{ __('permission-create.alias') }}</label>
        <input type="text" name="alias" class="form-control form-control-lg" id="permission-alias"
            value="{{ old('alias', $row->alias ?? request()->input('alias')) }}" readonly required>
        <small style="display: block; margin-top: 0.5rem; line-height: 1.5;">
            <ul style="margin: 0; padding-left: 1.2rem;">
                <li>Tự động tạo từ tên quyền.</li>
                <li>Chữ thường, bỏ dấu tiếng Việt, thay dấu cách bằng gạch ngang.</li>
                <li>Chỉ giữ chữ, số, gạch ngang.</li>
                <li>Ví dụ: access-user-list.</li>
            </ul>
        </small>
    </div>

    <div class="p-2">
        <label for="permission-description" class="form-label">{{ __('permission-create.description') }}</label>
        <input type="text" name="description" class="form-control form-control-lg" id="permission-description"
            value="{{ old('description', $row->description ?? request()->input('description')) }}">
        <small style="display: block; margin-top: 0.5rem; line-height: 1.5;">
            <ul style="margin: 0; padding-left: 1.2rem;">
                <li>Không bắt buộc.</li>
                <li>Tối đa 255 ký tự.</li>
                <li>Ví dụ: Cho phép xem danh sách người dùng.</li>
            </ul>
        </small>
    </div>

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
        <small style="display: block; margin-top: 0.5rem; line-height: 1.5;">
            <ul style="margin: 0; padding-left: 1.2rem;">
                <li>Chọn quyền cha hoặc "Không có quyền cha".</li>
                <li>Mặc định: Không có quyền cha.</li>
            </ul>
        </small>
    </div>

    <div class="p-2">
        <label for="permission-menu-route-name" class="form-label">{{ __('permission-create.menu_route_name') }}</label>
        <input type="text" name="menu_route_name" class="form-control form-control-lg" id="permission-menu-route-name"
            value="{{ old('menu_route_name', $row->menu_route_name ?? request()->input('menu_route_name')) }}"
            {{ old('is_menu', $row->is_menu ?? 0) ? 'required' : '' }}>
        <small style="display: block; margin-top: 0.5rem; line-height: 1.5;">
            <ul style="margin: 0; padding-left: 1.2rem;">
                <li>Bắt buộc nếu bật "Hiển thị trong menu".</li>
                <li>Định dạng: abc.xyz.def.</li>
                <li>Không khoảng trắng, tối đa 100 ký tự.</li>
                <li>Phải duy nhất.</li>
                <li>Ví dụ: user.list.view.</li>
            </ul>
        </small>
    </div>

    <div class="p-2">
        <label for="permission-menu-route-uri" class="form-label">{{ __('permission-create.menu_route_uri') }}</label>
        <input type="text" name="menu_route_uri" class="form-control form-control-lg" id="permission-menu-route-uri"
            value="{{ old('menu_route_uri', $row->menu_route_uri ?? request()->input('menu_route_uri')) }}"
            {{ old('is_menu', $row->is_menu ?? 0) ? 'required' : '' }}>
        <small style="display: block; margin-top: 0.5rem; line-height: 1.5;">
            <ul style="margin: 0; padding-left: 1.2rem;">
                <li>Bắt buộc nếu bật "Hiển thị trong menu".</li>
                <li>Không bắt đầu bằng "/".</li>
                <li>Phải duy nhất.</li>
                <li>Bao gồm biến nếu có.</li>
                <li>Ví dụ: users/list, campaign/{id}.</li>
            </ul>
        </small>
    </div>

    <div class="p-2">
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" name="is_menu" id="permission-is-menu"
                value="1" {{ old('is_menu', $row->is_menu ?? 0) ? 'checked' : '' }}>
            <label class="form-check-label" for="permission-is-menu">{{ __('permission-create.is_menu') }}</label>
        </div>
        <small style="display: block; margin-top: 0.5rem; line-height: 1.5;">
            <ul style="margin: 0; padding-left: 1.2rem;">
                <li>Bật nếu quyền hiển thị trên menu.</li>
                <li>Mặc định: Tắt.</li>
                <li>Nếu bật, các trường menu sẽ hiển thị và bắt buộc.</li>
            </ul>
        </small>
    </div>

    <div class="p-2 menu-dependent" style="display: {{ old('is_menu', $row->is_menu ?? 0) ? 'block' : 'none' }};">
        <label for="permission-menu-name" class="form-label">{{ __('permission-create.menu_name') }}</label>
        <input type="text" name="menu_name" class="form-control form-control-lg" id="permission-menu-name"
            value="{{ old('menu_name', $row->menu_name ?? request()->input('menu_name')) }}"
            {{ old('is_menu', $row->is_menu ?? 0) ? 'required' : '' }}>
        <small style="display: block; margin-top: 0.5rem; line-height: 1.5;">
            <ul style="margin: 0; padding-left: 1.2rem;">
                <li>Bắt buộc nếu bật "Là Menu".</li>
                <li>Ngắn gọn, dễ hiểu.</li>
                <li>Ví dụ: Quản Lý Người Dùng.</li>
            </ul>
        </small>
    </div>

    <div class="p-2 menu-dependent" style="display: {{ old('is_menu', $row->is_menu ?? 0) ? 'block' : 'none' }};">
        <label for="permission-menu-icon" class="form-label">{{ __('permission-create.menu_icon') }}</label>
        <input type="text" name="menu_icon" class="form-control form-control-lg" id="permission-menu-icon"
            value="{{ old('menu_icon', $row->menu_icon ?? request()->input('menu_icon')) }}">
        <small style="display: block; margin-top: 0.5rem; line-height: 1.5;">
            <ul style="margin: 0; padding-left: 1.2rem;">
                <li>Không bắt buộc.</li>
                <li>Nhập tên biểu tượng theo hệ thống biểu tượng.</li>
                <li>Ví dụ: user.</li>
            </ul>
        </small>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const nameInput = document.getElementById('permission-name');
            const aliasInput = document.getElementById('permission-alias');
            const isMenuInput = document.getElementById('permission-is-menu');
            const menuRouteInput = document.getElementById('permission-menu-route-name');
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
                document.getElementById('permission-menu-route-uri').required = isChecked;
                document.getElementById('permission-menu-name').required = isChecked;
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
            toggleMenuFields();
        });
    </script>
@endpush