<div class="box p-5 mt-5">
    <!-- input code -->
    <div class="p-2">
        <label for="device-code" class="form-label">{{ __('device-update.code') }}</label>
        <div class="input-group">
            <input type="text" name="code" class="form-control form-control-lg" id="device-code"
                   value="{{ $REQUEST->input('code') }}" readonly required>
            <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.generate') }}"
                    data-password-generate="#device-code" data-password-generate-format="uuid"
                    tabindex="-1">@icon('refresh-cw', 'w-5 h-5')
            </button>
        </div>
    </div>
    <!-- input name -->
    <div class="p-2">
        <label for="device-name" class="form-label">{{ __('device-create.name') }}</label>
        <input type="text" name="name" class="form-control form-control-lg" id="device-name"
               value="{{ old('name', isset($row) ? $row->name : '') }}" required>
    </div>
    <!-- input model -->
    <div class="p-2">
        <label for="device-model" class="form-label">{{ __('device-create.model') }}</label>
        <input type="text" name="model" class="form-control form-control-lg" id="device-model"
               value="{{ old('model', isset($row) ? $row->model: '') }}" required>
    </div>
    <!-- input serial -->
    <div class="p-2">
        <label for="device-serial" class="form-label">{{ __('device-create.serial') }}</label>
        <input type="text" name="serial" class="form-control form-control-lg" id="device-serial"
               value="{{ old('serial', isset($row) ? $row->serial: '') }}" required>
    </div>
    <!-- input device_type_id -->
    <div class="p-2">
        <x-select name="device_type_id" :options="$device_types" value="id" text="name" id="device-create-type"
                  :label="__('device-create.device_type')" :placeholder="__('device-create.device_type-select')"
                  :selected="old('device_type_id', isset($row) ? $row->device_type_id : null)" required></x-select>
    </div>
    <!-- input phone_number -->
    <div class="p-2">
        <label for="device-phone_number" class="form-label">{{ __('device-create.phone_number') }}</label>
        <input type="tel" name="phone_number" class="form-control form-control-lg" id="device-phone_number"
               value="{{ old('phone_number',isset($row) ? $row->phone_number: '') }}" autocomplete="off">
    </div>
    <!-- input password -->
    <div class="flex-1 p-2">
        <label for="device-password" class="form-label">{{ __('device-create.password') }}</label>
        <div class="input-group">
            <input type="password" name="password" class="form-control form-control-lg" id="device-password"
                   value="{{ old('password', isset($row) ? $row->password: '') }}" step="1"/>
            <button type="button" class="input-group-text input-group-text-lg" title="{{ __('common.show') }}"
                    data-password-show="#device-password" tabindex="-1">@icon('eye', 'w-5 h-5')
            </button>
        </div>
    </div>
    <!-- input bookmark_id/vehicle_id -->
    <div class="p-2">
        <x-select name="vehicle_id" :options="$vehicles" value="id" text="name" id="device-create-vehicle"
                  :label="__('device-create.vehicle')" :placeholder="__('device-create.vehicle-select')"
                  :selected="old('vehicle_id', isset($row) ? $row->vehicle_id : null)"
        ></x-select>
    </div>

    <!-- Thêm checkbox "Have camera supported?" -->
    <div class="p-2">
        <div class="form-check">
            <!-- Input ẩn để đảm bảo giá trị 0 được gửi khi checkbox không được chọn -->
            <input type="hidden" name="camera_supported" value="0"/>
            <!-- Checkbox chính, gửi giá trị 1 khi được chọn -->
            <input type="checkbox" name="camera_supported" value="1" class="form-check-switch"
                   id="device-have-camera-supported"
                {{ $REQUEST->input('camera_supported', isset($row) ? $row->camera_supported : 0) ? 'checked' : '' }} />
            <label for="device-have-camera-supported"
                   class="form-check-label">{{ __('device-create.this-device-supports-camera') }}</label>
        </div>
    </div>

    <!-- Trường nhập số lượng camera hỗ trợ, mặc định ẩn -->
    <div class="p-2" id="camera-supported-field" style="display: none;">
        <label for="device-camera-maximum"
               class="form-label">{{ __('device-create.maximum-number-of-cameras') }}</label>
        <input type="number" name="camera_maximum" class="form-control form-control-lg" id="device-camera-maximum"
               value="{{ old('camera_maximum', isset($row) ? $row->camera_maximum : 1) }}"
               min="1" step="1" {{ isset($row) && $row->camera_supported ? '' : 'disabled' }}>
    </div>
    <!-- Enable AI -->
{{--    auth()->user()->isRoleRoot()--}}
    @if (auth()->user()->isRoleRoot())
        <div class="p-2">
            <div class="form-check">
                <input type="checkbox" name="enable_ai" id="device-enable_ai" value="1"
                       class="form-check-switch"
                    {{old('enable_ai', isset($row) ? $row->enable_ai : false) ? 'checked' : ''}} />
                <label for="device-enable_ai" class="form-check-label">{{__('device-create.enable-ai')}}</label>
            </div>
        </div>

        <div class="p-2">
            <div class="form-check">
                <input type="checkbox" name="enabled" value="1" class="form-check-switch"
                       id="device-enabled" {{ old('enabled', isset($row) ? $row->enabled  : false) ? 'checked' : '' }} />
                <label for="device-enabled" class="form-check-label">{{ __('device-create.enabled') }}</label>
            </div>
        </div>
    @endif


    <div class="p-2">
        <div class="form-check">
            <input type="checkbox" name="shared" value="1" class="form-check-switch"
                   id="device-shared" {{ old('shared', isset($row) ? $row->shared : false) ? 'checked' : '' }} />
            <label for="device-shared" class="form-check-label">{{ __('device-create.shared') }}</label>
        </div>
    </div>

    <div class="p-2">
        <div class="form-check">
            <input type="checkbox" name="shared_public" value="1" class="form-check-switch"
                   id="device-shared_public" {{ old('shared_public', isset($row) ? $row->shared_public : false) ? 'checked' : '' }}
            / >
            <label for="device-shared_public" class="form-check-label">{{ __('device-create.shared_public') }}</label>
        </div>
    </div>
</div>

@push('scripts')
    <!-- JavaScript để hiển thị/ẩn trường camera_maximum -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const checkbox = document.getElementById('device-have-camera-supported');
            const cameraSupportedField = document.getElementById('camera-supported-field');

            // Kiểm tra trạng thái ban đầu của checkbox
            if (checkbox.checked) {
                cameraSupportedField.style.display = 'block';
            }

            const cameraMaximumField = document.getElementById('device-camera-maximum');

            // Thêm sự kiện khi checkbox thay đổi
            checkbox.addEventListener('change', function () {
                if (this.checked) {
                    cameraSupportedField.style.display = 'block';
                    cameraMaximumField.setAttribute('required', 'required');
                    cameraMaximumField.removeAttribute('disabled');
                    cameraMaximumField.value = '{{ $REQUEST->input('camera_maximum', isset($row) ? $row->camera_maximum : 1) }}';
                } else {
                    cameraSupportedField.style.display = 'none';
                    cameraMaximumField.value = '';
                    cameraMaximumField.removeAttribute('required');
                    cameraMaximumField.setAttribute('disabled', 'disabled');
                }
            });
        });
    </script>
@endpush
