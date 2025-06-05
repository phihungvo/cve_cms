@php
    use Illuminate\Support\Carbon;
    use App\Domains\Device\Enums\DetectedObject;
    use App\Domains\Device\Enums\RuleType;
@endphp

@extends('domains.device.rt-analytics-layout')

@section('content-analytics')
    <div class="intro-y box p-5 mt-5">
        <!-- List rule đã thêm -->
        <div class="grid grid-cols-1 md:grid-cols-12 gap-4">
            <div class="col-span-2 flex flex-col  border border-gray-300 rounded-md p-2">
                <h3 class="px-4 py-3 font-semibold text-gray-800"> {{__('Added rules') }}</h3>
                <ul id="ruleList">
                    @foreach($instance->instanceRules as $rule)
                        <div class="flex items-center justify-between">
                            <li data-rule-id="{{$rule->id}}" class="rule-item px-2 py-1 rounded font-medium text-sm
                             bg-white hover:bg-blue-100 focus:bg-blue-500 focus:text-white transition-colors
                             cursor-pointer w-full overflow-hidden text-ellipsis whitespace-normal line-clamp-2">
                                {{ $rule->name }}
                            </li>
                            <button type="button" data-rule-id="{{ $rule->id }}" onclick="deleteRule({{ $rule->id }})"
                                    class="btn-delete-rule ml-2 px-2 py-1
                                        rounded text-red-500 hover:text-red-700 hover:bg-blue-100">
                                x
                            </button>
                        </div>
                    @endforeach
                </ul>
            </div>
            {{-- Sidebar: Rule Types--}}
            <div class="col-span-2">
                <!-- Content for the first column (1/3) -->
                <ul class="flex flex-col justify-between gap-1 h-full">
                    @foreach(RuleType::cases() as $ruleType)
                        <li class="ruleType btn btn-outline-secondary py-4 cursor-pointer"
                            data-rule-type="{{ $ruleType->value }}">
                            {{ str_replace('_', ' ', ucfirst($ruleType->value)) }}
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="col-span-8">
                <!-- Content for the second column (2/3) -->
                <!-- content left -->
                <div class="w-full h-full flex flex-col border border-gray-300 rounded-md p-2">
                    <!-- rules name -->
                    <h2 class="text-sm font-bold py-2 w-full">Rule&nbsp;Name</h2>
                    <div class="w-full h-full grid grid-cols-1 md:grid-cols-3 gap-2">
                        <div class="col-span-1">
                            <div>
                                <input class="border border-gray-300 focous:border-blue-700 rounded-lg w-full p-2 text-sm"
                                       type="text"
                                       name="rule_name"
                                       id="rule_name" placeholder="Enter rules name">
                                <span id="ruleNameError" class="text-red-500 text-sm hidden">
                                    Please enter a rule name, it is required.
                                </span>
                            </div>
                            <div>
                                <h3 class="text-sm font-bold py-2 w-full">Object types to detect</h3>
                                <div class="flex flex-col gap-4">
                                    @foreach(DetectedObject::cases() as $type)
                                        <div class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="detect_objects"
                                                   id="object_type_{{ $type->value }}"
                                                   value="{{ $type->value }}">
                                            <label class="cursor-pointer"
                                                   for="object_type_{{ $type->value }}">{{ $type->value }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <span id="detectObjectCheckboxError" class="text-red-500 text-sm hidden">
                                    Please select at least one object type to detect.
                                </span>
                            </div>
                        </div>
                        <!-- view camera -->
                        <div class="col-span-2 border p-2 ">
                            <div class="w-full h-full ">
                                <h3>live view camera</h3>
                                <video class="hls-video" width="100%" height="400" controls autoplay>
                                    <source src="{{$instance->input_source}}" type="application/x-mpegURL">
                                    Your browser does not support the video tag.
                                </video>
                                <button type="button" class="p-2 cursor-pointer" onclick="openDrawingTool()">
                                    <svg fill="#000000" height="16px" width="16px" version="1.1" id="Capa_1"
                                         xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"
                                         viewBox="0 0 469 469" xml:space="preserve">
                                        <g>
                                            <g>
                                                <path d="M455.5,0h-442C6,0,0,6,0,13.5v211.9c0,7.5,6,13.5,13.5,13.5s13.5-6,13.5-13.5V27h415v415H242.4c-7.5,0-13.5,6-13.5,13.5
                                                s6,13.5,13.5,13.5h213.1c7.5,0,13.5-6,13.5-13.5v-442C469,6,463,0,455.5,0z"/>
                                                <path d="M175.6,279.9H13.5c-7.5,0-13.5,6-13.5,13.5v162.1C0,463,6,469,13.5,469h162.1c7.5,0,13.5-6,13.5-13.5V293.4
                                                C189.1,286,183,279.9,175.6,279.9z M162.1,442H27V306.9h135.1V442z"/>
                                                <path d="M360.4,127.7v71.5c0,7.5,6,13.5,13.5,13.5s13.5-6,13.5-13.5V95.1c0-7.5-6-13.5-13.5-13.5H269.8c-7.5,0-13.5,6-13.5,13.5
                                                s6,13.5,13.5,13.5h71.5L212.5,237.4c-5.3,5.3-5.3,13.8,0,19.1c2.6,2.6,6.1,4,9.5,4s6.9-1.3,9.5-4L360.4,127.7z"/>
                                            </g>
                                        </g>
                                </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="control grid grid-cols-1 md:grid-cols-12 mt-4">
            <!-- Button Update rule -->
            <button id="btn-save-rule" class="btn btn-secondary col-start-8 bg-white hover:bg-blue-500
            hover:text-white transition-colors" onclick="saveRule()">Add ++
            </button>
            <!-- Button Cancel -->
            <a href="{{route('device.runtime-analytics',['id'=> $row->id])}}" class="btn btn-secondary ml-2">
                {{__('Cancel')}}
            </a>
        </div>
    </div>
@stop

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
        const ruleNameEl = document.getElementById('rule_name');
        const detectObjectsCheckboxes = document.querySelectorAll('input[name="detect_objects"]');
        const ruleTypeButtons = document.querySelectorAll('.ruleType');
        let ruleItemsEl = document.querySelectorAll('.rule-item');

        @foreach($instance->instanceRules as $rule)
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

        function openDrawingTool() {
            const detectObjects = Array.from(detectObjectsCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            const ruleName = ruleNameEl.value.trim();

            const ruleNameError = document.getElementById('ruleNameError');
            const detectObjectCheckboxError = document.getElementById('detectObjectCheckboxError');
            [ruleNameError, detectObjectCheckboxError].forEach(el => el.classList.add('hidden'));

            if (!ruleName || detectObjects.length === 0) {
                if (!ruleName) {
                    ruleNameError.classList.remove('hidden');
                }
                if (detectObjects.length === 0) {
                    detectObjectCheckboxError.classList.remove('hidden');
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

                ruleItemsEl.forEach(el => {
                    el.classList.remove('bg-blue-500', 'text-white');
                    el.classList.add('bg-white');
                });

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

            const ruleName = document.getElementById('rule_name').value.trim();
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
                device_cvedixrt_instance_id: instanceId
            }
            console.log('Request data:', requestData);

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
                            document.getElementById('btn-save-rule').innerText = 'Add +';

                            if (window.DrawingTool) {
                                window.DrawingTool.loadShapesFromServer(null);
                            }
                        });
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

                handleRuleItemClick(ruleId)

                const btnSaveRule = document.getElementById('btn-save-rule');
                btnSaveRule.innerText = 'Update +';

                updateRuleConfiguration(selectedAddedRule);

                window.DrawingTool.loadShapesFromServer(selectedAddedRule);
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

                    const btnSaveRule = document.getElementById('btn-save-rule');
                    btnSaveRule.innerText = 'Update +';

                    updateRuleConfiguration(selectedAddedRule);
                    console.log('Selected rule:', selectedAddedRule);

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
                // Reset checkbox DetectedObject
                detectObjectsCheckboxes.forEach(checkbox => {
                    checkbox.checked = false;
                });

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
            const buttons = document.querySelectorAll('.ruleType');
            if (buttons.length > 0) {
                buttons[0].classList.remove('bg-white');
                buttons[0].classList.add('bg-blue-500', 'text-white');
                selectedRuleType = buttons[0].dataset.ruleType;
            }
            buttons.forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    buttons.forEach(b => {
                        b.classList.remove('bg-blue-500', 'text-white');
                        b.classList.add('bg-white');
                    });
                    btn.classList.remove('bg-white');
                    btn.classList.add('bg-blue-500', 'text-white');
                    selectedRuleType = btn.dataset.ruleType;

                    ruleNameEl.value = '';
                    selectedAddedRule = null;
                    window.tempShapesToSave = null;
                    window.shapes = [];
                    window.tempShapes = [];

                    detectObjectsCheckboxes.forEach(checkbox => {
                        checkbox.checked = false;
                    });

                    ruleItemsEl.forEach(el => {
                        el.classList.remove('bg-blue-500', 'text-white');
                        el.classList.add('bg-white');
                    });

                    const btnSaveRule = document.getElementById('btn-save-rule');
                    btnSaveRule.innerText = 'Add +';

                    if (window.DrawingTool) {
                        window.DrawingTool.loadShapesFromServer(null); // Xóa shapes hiện tại
                    }
                });
            });
        });
    </script>
@endpush
