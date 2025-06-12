@php
    use Illuminate\Support\Carbon;
    use App\Domains\Cvedixrt\Instance\Enums\DetectedObject;
    use App\Domains\Cvedixrt\Instance\Enums\RuleType;
@endphp
@extends('layouts.in')

@section('body')
    <div class="flex rounded shadow bg-white min-h-[600px]">
        <!-- List Rule đã thêm -->
        <div class="w-1/3 flex flex-col bg-gray-50 border-r">
            <h3 class="px-4 py-3 font-semibold text-gray-800">{{ __('cvedixt-analytic.added_rule') }}</h3>
            <ul id="ruleList" class="flex-1 py-4 px-2 space-y-1 overflow-y-auto max-h-[32rem]">
                @foreach($row->instanceRules as $rule)
                    <div class="flex items-center justify-between">
                        <li data-rule-id="{{$rule->id}}"
                            class="rule-item block px-2 py-1 rounded font-medium text-sm
                            bg-white hover:bg-blue-100 focus:bg-blue-500 focus:text-white transition-colors cursor-pointer w-full">
                            {{$rule->name}}
                        </li>
                        <button type="button" data-rule-id="{{$rule->id}}" class="btn-delete-rule ml-2 px-2 py-1
                            rounded text-red-500 hover:text-red-700 hover:bg-blue-100"
                                onclick="deleteRule({{ $rule->id }})">
                            ×
                        </button>
                    </div>
                @endforeach
            </ul>
        </div>
        <!-- Sidebar: Rule Types -->
        <div class="w-1/3 bg-white flex flex-col">
            <a id="generalSettingBtn"
               onmousedown="if(event.detail > 1) event.preventDefault();"
               class="btn mb-3 border-2 border-primary text-sm text-left justify-start
                       bg-white hover:bg-blue-100 focus:text-white transition-colors cursor-pointer
                        ml-3 px-4 mt-5 font-semibold text-gray-800">
                {{ __('cvedixt-analytic.general_settings') }}
            </a>
            <hr class="my-2 border-gray-300 ml-3">
            <h3 class="px-4 py-3 font-semibold">{{ __('cvedixt-analytic.analytics_rules') }}</h3>
            <div class="flex flex-col flex-1 space-y-2 ml-3 mt-3">
                @foreach(RuleType::cases() as $rule)
                    <a href="#"
                       class="btn form-control-lg mb-3 border-2 border-primary text-sm text-left justify-start
                       bg-white hover:bg-blue-100 focus:text-white transition-colors cursor-pointer"
                       data-rule-type="{{ $rule->value }}">
                        {{ str_replace('_', ' ', ucfirst($rule->value)) }}
                    </a>
                @endforeach
            </div>
        </div>
        <!-- Rule Configuration -->
        <div id="ruleConfiguration" class="w-full p-6 hidden">
            <div class="flex border border-gray-400 p-4">
                <!-- Left column: Rule name + Object types -->
                <div class="w-1/2 pr-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">{{ __('cvedixt-analytic.rule_name') }}</label>
                        <input type="text" id="ruleNameInput" value="{{ old('name') }}"
                               class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                        <div id="ruleNameError"
                             class="text-red-500 text-sm mt-1 hidden">{{ __('cvedixt-analytic.alert_no_rule_name') }}</div>
                    </div>
                    <div class="mb-4">
                        <div class="mb-2 font-medium">{{ __('cvedixt-analytic.object_detection') }}</div>
                        <div class="space-y-2">
                            @foreach(DetectedObject::cases() as $type)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="detect_objects" value="{{ $type->value }}"
                                           class="accent-blue-500 cursor-pointer">
                                    <span class="text-blue-600 cursor-pointer">{{ $type->value }}</span>
                                </label>
                            @endforeach
                        </div>
                        <div id="detectObjectsError"
                             class="text-red-500 text-sm mt-1 hidden">{{ __('cvedixt-analytic.alert_no_object') }}</div>
                    </div>
                </div>
                <!-- Right column: Camera live -->
                <div class="w-1/2 pl-4">
                    <div class="mb-2 font-medium">{{ __('cvedixt-analytic.live_camera') }}</div>
                    <div
                        class="border border-gray-300 rounded bg-white h-40 w-full flex items-center justify-center text-black">
                        <span class="text-sm">
                            <a href="#" onclick="openDrawingTool()" class="text-blue-600 hover:text-blue-800">
                                {{ __('cvedixt-analytic.click_to_draw') }}
                            </a>
                        </span>
                    </div>
                </div>
            </div>
            <div class="flex justify-center mt-4">
                <a href="#" onclick="saveRule()" id="btn-save-rule"
                   class="w-48 text-center px-12 py-2 border border-gray-500 rounded bg-white hover:bg-gray-100">
                    {{ __('cvedixt-analytic.add') }}
                </a>
            </div>
            <div class="flex justify-end space-x-4 mt-4">
                <a href="#"
                   class="w-32 text-center py-2 rounded bg-cyan-100 text-black border border-cyan-400 hover:bg-cyan-200">{{ __('cvedixt-analytic.ok') }}</a>
                <a href="#"
                   class="w-32 text-center py-2 rounded bg-cyan-100 text-black border border-cyan-400 hover:bg-cyan-200">{{ __('cvedixt-analytic.apply') }}</a>
                <a href="#"
                   class="w-32 text-center py-2 rounded border border-gray-400 hover:bg-gray-100">{{ __('cvedixt-analytic.cancel') }}</a>
            </div>
        </div>

        <div id="generalSetting" class="w-full p-6">
            <div>
                <h3 class="text-lg font-semibold mb-4">{{ __('cvedixt-analytic.general_settings') }}</h3>
                <div class="gap-x-6 gap-y-6 items-start">
                    <div class="space-y-6">
                        <div class="flex flex-row mb-3">
                            <label for="auto-start" class="w-1/3 font-medium block mb-2">Auto Start</label>
                            <input type="checkbox" id="auto-start" name="auto_start" value="1"
                                   class="form-check-switch">
                        </div>
                        <!-- Auto Restart -->
                        <div class="flex flex-row mb-3">
                            <label for="auto-restart" class="w-1/3 font-medium block mb-2">Auto Restart</label>
                            <input type="checkbox" id="auto-restart" name="auto_restart" value="1"
                                   class="form-check-switch">
                        </div>
                        <div class="flex flex-row mb-3 items-center">
                            <!-- Profile -->
                            <label for="profile" class="w-1/3 font-medium block mb-2">Profile</label>
                            <select id="profile" name="profile" class="w-1/2 form-select form-select-lg bg-white"
                                    data-change-submit="data-change-submit">
                                <option value="standard">Standard</option>
                                <option value="normal">Normal</option>
                                <option value="low">Low</option>
                            </select>
                            <span class="ml-2 cursor-pointer" title="Select the profile for camera analytics">
                           <svg xmlns="http://www.w3.org/2000/svg" class="inline w-8 h-8" fill="#e0f2fe"
                                viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" fill="#e0f2fe" stroke="#38bdf8" stroke-width="1"/>
                                    <text x="12" y="16" text-anchor="middle" font-size="16" fill="#38bdf8"
                                          font-family="Arial" dy="0.1em">?</text>
                                </svg>
                            </span>
                        </div>
                        <div class="flex flex-row items-center">
                            <!-- Camera Orientation -->
                            <label for="camera-orientation" class="w-1/3 font-medium block mb-2">Camera
                                Orientation</label>
                            <select id="camera-orientation" name="camera_orientation"
                                    class="w-1/2 form-select form-select-lg bg-white"
                                    data-change-submit="data-change-submit">
                                <option value="0">0</option>
                                <option value="45">45</option>
                                <option value="90">90</option>
                                <option value="180">180</option>
                            </select>
                            <span class="ml-2 cursor-pointer" title="Select the profile for camera analytics">
                                <svg xmlns="http://www.w3.org/2000/svg" class="inline w-8 h-8" fill="#e0f2fe"
                                     viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" fill="#e0f2fe" stroke="#38bdf8" stroke-width="1"/>
                                    <text x="12" y="16" text-anchor="middle" font-size="16" fill="#38bdf8"
                                          font-family="Arial" dy="0.1em">?</text>
                                </svg>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        @endsection

        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="{{ asset('/js/drawing-tool.js') }}"></script>
            <script>
                const instanceId = {{ $row->id }};
                const instanceUuid = '{{ $row->uuid }}';
                let selectedRuleType = 'line_crossing';
                let selectedAddedRule = null;
                let instanceRules = [];

                const ruleList = document.getElementById('ruleList');
                const ruleNameEl = document.getElementById('ruleNameInput');
                const detectObjectsCheckboxes = document.querySelectorAll('input[name="detect_objects"]');
                const ruleTypeButtons = document.querySelectorAll('.btn.form-control-lg');
                let ruleItemsEl = document.querySelectorAll('.rule-item');

                const generalSettingBtn = document.getElementById('generalSettingBtn');
                const generalSettingDiv = document.getElementById('generalSetting');

                generalSettingBtn.addEventListener('click', function (e) {
                    e.preventDefault();
                    generalSettingDiv.classList.remove('hidden');
                    document.getElementById('ruleConfiguration').classList.add('hidden');
                });

                function loadInstanceRules() {
                    @foreach($row->instanceRules as $rule)
                    instanceRules.push({
                        id: {{ $rule->id }},
                        uuid: '{{ $rule->uuid }}',
                        name: '{{ $rule->name }}',
                        detected_object: @json($rule->detected_object),
                        rule_type: '{{ $rule->rule_type }}',
                        drawing_object: @json($rule->drawing_object),
                        direction: '{{ $rule->direction }}',
                        cvedixrt_instance_id: {{ $rule->cvedixrt_instance_id }},
                    });
                    @endforeach
                }

                loadInstanceRules();

                function openDrawingTool() {
                    if (!instanceId || !instanceUuid) {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('cvedixt-analytic.alert_missing_instance') }}',
                        });
                        return;
                    }

                    // Lấy danh sách đối tượng phát hiện
                    const detectObjects = Array.from(detectObjectsCheckboxes)
                        .filter(cb => cb.checked)
                        .map(cb => cb.value);

                    // Lấy tên quy tắc
                    const ruleName = ruleNameEl.value.trim();

                    // Hiển thị thông báo lỗi
                    const ruleNameError = document.getElementById('ruleNameError');
                    const detectObjectsError = document.getElementById('detectObjectsError');
                    [ruleNameError, detectObjectsError].forEach(el => el.classList.add('hidden'));

                    if (!ruleName || detectObjects.length === 0) {
                        if (!ruleName) {
                            ruleNameError.classList.remove('hidden');
                        }
                        if (detectObjects.length === 0) {
                            detectObjectsError.classList.remove('hidden');
                        }
                        return;
                    }

                    if (window.DrawingTool) {
                        window.DrawingTool.open(instanceId, instanceUuid, {
                            rule_type: selectedRuleType,
                            detect_objects: detectObjects,
                            rule_name: ruleName
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '{{ __('cvedixt-analytic.error_drawing_tool') }}'
                        });
                    }
                }

                /**
                 * Hàm sử lý khi click vào rule item
                 * Clear form trống chuẩn bị sẵn sàng cho create mới instance_rule
                 * @param ruleId
                 */
                function handleRuleItemClick(ruleId) {
                    selectedAddedRule = instanceRules.find(rule => rule.id === ruleId);

                    // Xóa trạng thái active của các rule khác
                    ruleItemsEl.forEach(el => {
                        el.classList.remove('bg-blue-500', 'text-white');
                        el.classList.add('bg-white');
                    });

                    // Thêm trạng thái active cho rule được chọn
                    const currentRuleItem = document.querySelector(`.rule-item[data-rule-id="${ruleId}"]`);
                    currentRuleItem.classList.remove('bg-white');
                    currentRuleItem.classList.add('bg-blue-500', 'text-white');

                    const btnSaveRule = document.getElementById('btn-save-rule');
                    btnSaveRule.innerText = 'Update +';

                    // Cập nhật form cấu hình
                    updateRuleConfiguration(selectedAddedRule);
                }

                /**
                 * Cập nhật lại mảng danh sách ruleList
                 * Cập nhật lại các element trên UI
                 */
                function updateElement(addedRule) {
                    // Cập nhật danh sách rule đã thêm
                    instanceRules.push({
                        id: addedRule.id,
                        uuid: addedRule.uuid,
                        name: addedRule.name,
                        detected_object: addedRule.detected_object,
                        rule_type: addedRule.rule_type,
                        drawing_object: addedRule.drawing_object,
                        direction: addedRule.direction,
                        cvedixrt_instance_id: addedRule.cvedixrt_instance_id,
                    })

                    // Gọi lại hàm render thẻ li
                    const ruleList = document.querySelector('.rule-item');
                    const newRuleItemContainer = document.createElement('div');
                    newRuleItemContainer.className = 'flex items-center justify-between';

                    const newRuleItem = document.createElement('li');
                    newRuleItem.setAttribute('data-rule-id', addedRule.id);
                    newRuleItem.className = 'rule-item block px-2 py-1 rounded font-medium text-sm bg-white hover:bg-blue-100 focus:bg-blue-500 focus:text-white transition-colors cursor-pointer';
                    newRuleItem.textContent = addedRule.name;

                    newRuleItem.addEventListener('click', function () {
                        let ruleId = parseInt(this.getAttribute('data-rule-id'), 10);
                        selectedAddedRule = instanceRules.find(rule => rule.id === ruleId);
                        console.log(selectedAddedRule)

                        // Xóa trạng thái active của các rule khác
                        ruleItemsEl.forEach(el => {
                            el.classList.remove('bg-blue-500', 'text-white');
                            el.classList.add('bg-white');
                        });

                        // Thêm trạng thái active cho rule được chọn
                        this.classList.remove('bg-white');
                        this.classList.add('bg-blue-500', 'text-white');

                        const btnSaveRule = document.getElementById('btn-save-rule');
                        btnSaveRule.innerText = 'Update +';

                        // Cập nhật form cấu hình
                        updateRuleConfiguration(selectedAddedRule);
                    })

                    const deleteButton = document.createElement('button');
                    deleteButton.setAttribute('type', 'button');
                    deleteButton.setAttribute('data-rule-id', addedRule.id);
                    deleteButton.className = 'btn-delete-rule ml-2 px-2 py-1 rounded text-red-500 hover:text-red-700 hover:bg-blue-100';
                    deleteButton.textContent = '×';
                    deleteButton.setAttribute('onclick', `deleteRule(${addedRule.id})`);

                    newRuleItemContainer.appendChild(newRuleItem);
                    newRuleItemContainer.appendChild(deleteButton);

                    ruleList.appendChild(newRuleItemContainer);
                }

                /**
                 * Hàm xử lý button khi người dùng nhấn vào
                 * Có 2 trường hợp xảy ra là create vs update
                 */
                function saveRule() {
                    const action = selectedAddedRule ? 'updateInstanceRule' : 'createInstanceRule';

                    const ruleName = document.getElementById('ruleNameInput').value.trim();
                    const detectObjects = Array.from(document.querySelectorAll('input[name="detect_objects"]:checked'))
                        .map(cb => cb.value);
                    const ruleType = selectedRuleType;
                    const ruleId = selectedAddedRule ? selectedAddedRule.id : null;
                    const uuid = window.tempShapesToSave?.uuid || selectedAddedRule?.uuid || instanceUuid;
                    const drawingObject = window.tempShapesToSave?.drawing_object
                        || selectedAddedRule?.drawing_object || [];
                    const direction = window.tempShapesToSave?.direction || selectedAddedRule.direction;

                    const requestData = {
                        _action: action,
                        uuid: uuid,
                        name: ruleName,
                        detected_object: detectObjects,
                        rule_type: ruleType,
                        drawing_object: drawingObject,
                        direction: direction,
                        cvedixrt_instance_id: instanceId
                    }

                    if (action === 'updateInstanceRule') {
                        requestData.rule_id = ruleId;
                    }

                    fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        },
                        body: JSON.stringify(requestData)
                    })
                        .then(response => {
                            if (!response.ok) {
                                return response.json().then(err => Promise.reject(err));
                            }
                            return response.json();
                        })
                        .then(data => {
                            if (data.status) {
                                // Cập nhật instanceRules
                                if (action === 'createInstanceRule') {
                                    instanceRules.push(data.data);
                                } else {
                                    const index = instanceRules.findIndex(r => r.id === ruleId);
                                    if (index !== -1) {
                                        instanceRules[index] = data.data;
                                    }
                                }

                                Swal.fire({
                                    icon: 'success',
                                    title: 'Thành công',
                                    text: data.message,
                                    timer: 1200,
                                    showConfirmButton: false
                                }).then(() => {
                                    window.shapes = [];
                                    window.tempShapes = [];
                                    window.tempShapesToSave = null;
                                    selectedAddedRule = null;
                                    updateRuleList(data.data);
                                    updateRuleConfiguration();
                                    ruleItemsEl = document.querySelectorAll('.rule-item');
                                    selectedAddedRule = null;
                                    document.getElementById('btn-save-rule').innerText = 'Add +';

                                    if (window.DrawingTool) {
                                        window.DrawingTool.loadShapesFromServer(null);
                                    }
                                });
                            } else {
                                throw new Error(data.message || 'Không thể lưu rule');
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Lỗi',
                                text: 'Không thể lưu rule: ' + error.message
                            });
                        });
                }

                ruleItemsEl.forEach(item => {
                    item.addEventListener('click', function () {
                        let ruleId = parseInt(this.getAttribute('data-rule-id'), 10);
                        // gọi hàm
                        handleRuleItemClick(ruleId);

                        // Thêm trạng thái active cho rule được chọn
                        this.classList.remove('bg-white');
                        this.classList.add('bg-blue-500', 'text-white');

                        const btnSaveRule = document.getElementById('btn-save-rule');
                        btnSaveRule.innerText = 'Update +';

                        selectedAddedRule = instanceRules.find(rule => rule.id === ruleId);
                        if (selectedAddedRule && window.DrawingTool) {
                            // Đảm bảo selectedAddedRule có drawing_object trước khi load
                            window.DrawingTool.loadShapesFromServer(selectedAddedRule);
                        }
                    });
                });

                /**
                 * Cập nhật list item rule trên UI.
                 * @param instanceRule
                 */
                function updateRuleList(instanceRule) {
                    let existingRuleItem = document.querySelector(`.rule-item[data-rule-id="${instanceRule.id}"]`);

                    if (existingRuleItem) {
                        existingRuleItem.textContent = instanceRule.name;
                    } else {
                        let instanceRuleEL = document.createElement('div');
                        instanceRuleEL.className = 'flex items-center justify-between';

                        let li = document.createElement('li');
                        li.className = 'rule-item block px-2 py-1 rounded font-medium text-sm bg-white hover:bg-blue-100 cursor-pointer w-full';
                        li.setAttribute('data-rule-id', instanceRule.id);
                        li.textContent = instanceRule.name;

                        li.addEventListener('click', function () {
                            const ruleId = parseInt(this.getAttribute('data-rule-id'), 10);
                            handleRuleItemClick(ruleId)
                            selectedAddedRule = instanceRules.find(r => r.id === ruleId);

                            document.querySelectorAll('.rule-item').forEach(el => {
                                el.classList.remove('bg-blue-500', 'text-white');
                                el.classList.add('bg-white');
                            });

                            this.classList.remove('bg-white');
                            this.classList.add('bg-blue-500', 'text-white');

                            const btnSaveRule = document.getElementById('btn-save-rule');
                            btnSaveRule.innerText = 'Update +';

                            updateRuleConfiguration(selectedAddedRule);

                            if (window.DrawingTool) {
                                window.DrawingTool.loadShapesFromServer(selectedAddedRule);
                            }

                        });

                        let deleteBtn = document.createElement('button');
                        deleteBtn.type = 'button';
                        deleteBtn.setAttribute('data-rule-id', instanceRule.id);
                        deleteBtn.className = 'btn-delete-rule ml-2 px-2 py-1 rounded text-red-500 hover:text-red-700 hover:bg-blue-100';
                        deleteBtn.textContent = '×';

                        deleteBtn.addEventListener('click', (e) => {
                            e.stopPropagation();
                            deleteRule(parseInt(deleteBtn.dataset.ruleId));
                        });

                        instanceRuleEL.appendChild(li);
                        instanceRuleEL.appendChild(deleteBtn);
                        ruleList.appendChild(instanceRuleEL);
                    }
                }

                /**
                 * Hàm cập nhật cấu hình rule
                 * @param dataCurrentRule
                 */
                function updateRuleConfiguration(dataCurrentRule = null) {
                    if (dataCurrentRule) {
                        ruleNameEl.value = dataCurrentRule.name;
                        detectObjectsCheckboxes.forEach(checkbox => {
                            checkbox.checked = dataCurrentRule.detected_object.includes(checkbox.value);
                        });
                        ruleTypeButtons.forEach(btn => {
                            if (btn.dataset.ruleType === dataCurrentRule?.rule_type) {
                                btn.classList.remove('bg-white');
                                btn.classList.add('bg-blue-500', 'text-white');
                                selectedRuleType = dataCurrentRule.rule_type;
                            } else {
                                btn.classList.remove('bg-blue-500', 'text-white', 'focus:bg-blue-500', 'focus:text-white');
                                btn.classList.add('bg-white');
                            }
                        });

                        ruleItemsEl.forEach(item => {
                            if (item.dataset.ruleId === String(dataCurrentRule.id)) {
                                item.classList.remove('bg-white');
                                item.classList.add('bg-blue-500', 'text-white');
                            } else {
                                item.classList.remove('bg-blue-500', 'text-white');
                                item.classList.add('bg-white');
                            }
                        })
                    } else {
                        ruleNameEl.value = '';
                        // Reset các checkbox của DetectedObject
                        detectObjectsCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });

                        // Cập nhật rule type
                        ruleTypeButtons.forEach(btn => {
                            btn.classList.remove('bg-blue-500', 'text-white');
                            btn.classList.add('bg-white');
                        });

                        ruleItemsEl.forEach(item => {
                            item.classList.remove('bg-blue-500', 'text-white');
                            item.classList.add('bg-white');
                        })

                        ruleTypeButtons[0].classList.remove('bg-white');
                        ruleTypeButtons[0].classList.add('bg-blue-500', 'text-white');
                        selectedRuleType = ruleTypeButtons[0].dataset.ruleType;
                    }
                }

                function deleteRule(ruleId) {
                    Swal.fire({
                        title: 'Xác nhận xóa',
                        text: 'Bạn có chắc muốn xóa rule này?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Xóa',
                        cancelButtonText: 'Hủy'
                    }).then(result => {
                        if (result.isConfirmed) {
                            fetch(window.location.href, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'Accept': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                                },
                                body: JSON.stringify({
                                    _action: 'deleteInstanceRule',
                                    rule_id: ruleId,
                                })
                            })
                                .then(response => {
                                    if (!response.ok) {
                                        throw new Error('Network response was not ok');
                                    }
                                    return response.json();
                                })
                                .then(data => {
                                    const ruleItem = document.querySelector(`.rule-item[data-rule-id="${ruleId}"]`);
                                    const parentRuleItem = ruleItem.parentElement;
                                    if (ruleItem) {
                                        parentRuleItem.remove();
                                    }

                                    selectedAddedRule = null;
                                    instanceRules = instanceRules.filter(rule => rule.id !== ruleId);
                                    updateRuleConfiguration(selectedAddedRule);

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công',
                                        text: 'Đã xóa rule',
                                        timer: 1200
                                    });
                                })
                                .catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: '{{ __('cvedixt-analytic.error_deleting_rule') }}',
                                    });
                                });
                        }
                    });
                }

                document.addEventListener('DOMContentLoaded', () => {
                    const buttons = document.querySelectorAll('.btn.form-control-lg');
                    const generalSettingBtn = document.getElementById('generalSettingBtn');
                    const generalSettingDiv = document.getElementById('generalSetting');
                    const ruleConfiguration = document.getElementById('ruleConfiguration');

                    // Set initial active state for the first rule type button
                    if (buttons.length > 0) {
                        buttons[0].classList.remove('bg-white');
                        buttons[0].classList.add('bg-blue-500', 'text-white');
                        selectedRuleType = buttons[0].dataset.ruleType;
                    }

                    // Event listener for rule type buttons
                    buttons.forEach(btn => {
                        btn.addEventListener('click', e => {
                            e.preventDefault();
                            // Reset selected rule
                            selectedAddedRule = null;

                            // Show ruleConfiguration and hide generalSetting
                            ruleConfiguration.classList.remove('hidden');
                            generalSettingDiv.classList.add('hidden');

                            // Update button styles
                            buttons.forEach(b => {
                                b.classList.remove('bg-blue-500', 'text-white');
                                b.classList.add('bg-white');
                            });
                            btn.classList.remove('bg-white');
                            btn.classList.add('bg-blue-500', 'text-white');
                            selectedRuleType = btn.dataset.ruleType;

                            // Reset form and UI state
                            ruleNameEl.value = '';
                            detectObjectsCheckboxes.forEach(checkbox => {
                                checkbox.checked = false;
                            });

                            // Reset active state of rule items
                            ruleItemsEl.forEach(el => {
                                el.classList.remove('bg-blue-500', 'text-white');
                                el.classList.add('bg-white');
                            });

                            const btnSaveRule = document.getElementById('btn-save-rule');
                            btnSaveRule.innerText = 'Add +';

                            // Clear canvas shapes
                            window.shapes = [];
                            window.tempShapes = [];
                            window.tempShapesToSave = null;
                            if (window.DrawingTool) {
                                window.DrawingTool.loadShapesFromServer(null);
                            }
                        });
                    });

                    // Event listener for general setting button
                    generalSettingBtn.addEventListener('click', e => {
                        e.preventDefault();
                        generalSettingDiv.classList.remove('hidden');
                        ruleConfiguration.classList.add('hidden');

                        // Reset active state of rule type buttons
                        buttons.forEach(b => {
                            b.classList.remove('bg-blue-500', 'text-white');
                            b.classList.add('bg-white');
                        });

                        // Reset active state of rule items
                        ruleItemsEl.forEach(el => {
                            el.classList.remove('bg-blue-500', 'text-white');
                            el.classList.add('bg-white');
                        });

                        // Reset form
                        ruleNameEl.value = '';
                        detectObjectsCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });

                        const btnSaveRule = document.getElementById('btn-save-rule');
                        btnSaveRule.innerText = 'Add +';
                    });
                });
            </script>
    @endpush
