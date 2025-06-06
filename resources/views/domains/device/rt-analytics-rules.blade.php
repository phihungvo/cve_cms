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
                            rounded text-red-500 hover:text-red-700 hover:bg-blue-100">&times;
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
                                <input
                                    class="border border-gray-300 focous:border-blue-700 rounded-lg w-full p-2 text-sm"
                                    type="text"
                                    name="rule_name"
                                    id="rule_name" placeholder="Enter rules name">
                                <span id="ruleNameError" class="text-red-500 text-sm hidden">
                                    Please enter a rule name, it is required.
                                </span>
                            </div>
                            <div>
                                <h3 class="mt-2 py-2 text-sm font-bold py-2 w-full">Object types to detect</h3>
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
            <!-- content right -->
        </div>
        <div class="control grid grid-cols-1 md:grid-cols-12 mt-4">
            <!-- Button Update rule -->
            <button id="btn-save-rule" class="btn btn-secondary col-start-8 bg-white hover:bg-blue-500
            hover:text-white transition-colors" onclick="saveRule()">+&nbsp;Add
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
        // Define Global variables
        const id = {{ $row->id }}; // device id
        const instanceId = {{ $instance->id }}; // instance id
        const instanceUuid = ' {{ $instance->uuid }}'; // instance uuid

        let selectedAddedRule = null;
        let instanceRules = [];
        let selectedRuleType = '{{ RuleType::LINE_CROSSING->value }}'; // Default rule type

        const ruleList = document.getElementById('ruleList');
        const ruleNameEl = document.getElementById('rule_name');
        const detectedObjectEl = document.querySelectorAll('input[name="detect_objects"]');
        let ruleItemEls = document.querySelectorAll('.rule-item');
        const ruleTypeEls = document.querySelectorAll('.ruleType')
        const btnSaveRuleEl = document.getElementById('btn-save-rule');

        /**
         * Khai báo và thực thi hàm loadRules để nạp các quy tắc từ server.
         *
         * @type {loadRules}
         */
        const loadInstanceRules = (function () {
            function loadRules() {
                instanceRules = [];
                @foreach($instance->instanceRules as $rule)
                instanceRules.push({
                    id: {{ $rule->id }},
                    uuid: '{{ $rule->uuid }}',
                    name: '{{ $rule->name }}',
                    detected_object: @json($rule->detected_object),
                    rule_type: '{{ $rule->rule_type }}',
                    drawing_object: @json($rule->drawing_object),
                    direction: '{{ $rule->direction }}',
                    cvedixrt_instance_id: {{ $rule->device_cvedixrt_instance_id }},
                });
                @endforeach
            }

            loadRules();
            return loadRules
        })();

        function openDrawingTool() {
            const detectObjects = Array.from(detectedObjectEl)
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
         * Handle Sự kiện Click vào các rule đã thêm.
         * Reset các trạng thái active của các rule khác và thêm trạng thái active cho rule được chọn.
         */
        ruleItemEls.forEach(item => {
            item.addEventListener('click', function () {
                handleRuleItemClick(this);
            })
        });

        /**
         * Css active cho rule được chọn.
         * Xóa trạng thái active của các rule hiện.
         * @param item
         */
        function handleRuleItemClick(item) {
            const ruleId = parseInt(item.getAttribute('data-rule-id'), 10);
            selectedAddedRule = instanceRules.find(r => r.id === ruleId);

            // Xóa trạng thái active của các rule hiện có.
            ruleItemEls.forEach(el => {
                el.classList.remove('bg-blue-500', 'text-white');
                el.classList.add('bg-white');
            });

            // Thêm trạng thái active cho rule được chọn.
            const currentRuleItem = document.querySelector(`.rule-item[data-rule-id="${ruleId}"]`);
            currentRuleItem.classList.add('bg-blue-500', 'text-white');
            currentRuleItem.classList.remove('bg-white');

            //// Biding data cho form
            // Rule name
            ruleNameEl.value = selectedAddedRule.name;
            // Rule type
            const ruleType = selectedAddedRule.rule_type;
            ruleTypeEls.forEach(rt => {
                if (rt.getAttribute('data-rule-type') === ruleType) {
                    rt.classList.add('bg-blue-500', 'text-white');
                    rt.classList.remove('bg-white');
                } else {
                    rt.classList.remove('bg-blue-500', 'text-white');
                    rt.classList.add('bg-white');
                }
            })
            // Detected object
            detectedObjectEl.forEach(checkbox => {
                const objectType = checkbox.value;
                checkbox.checked = selectedAddedRule.detected_object.includes(objectType);
            });
            // Update value button Update rule
            btnSaveRuleEl.innerHTML = 'Update';

            // Load shapes for the selected rule into the drawing tool
            if (window.DrawingTool && selectedAddedRule) {
                window.DrawingTool.loadShapesFromServer(selectedAddedRule);
            }
        }

        /**
         * Xử lý sự kiện khi người dùng click vào một rule type.
         *
         */
        ruleTypeEls.forEach(item => {
            item.addEventListener('click', function () {

                // Xử lý UI khi người dùng click vào một rule type.
                handleRuleTypeClick(this);
                // clear form chuẩn bị cho create rule mới.

                if (selectedAddedRule) {
                    // Clear rule name input
                    ruleNameEl.value = '';
                    // Clear detected object checkboxes
                    detectedObjectEl.forEach(checkbox => {
                        checkbox.checked = false;
                    });
                }

                // Reset selectedAddedRule
                selectedAddedRule = null;

                // Update value selectedAddedRule
                selectedRuleType = this.getAttribute('data-rule-type');

                // Clear rule item active
                ruleItemEls.forEach(el => {
                    el.classList.remove('bg-blue-500', 'text-white');
                    el.classList.add('bg-white');
                });
                // Update value button Update rule
                btnSaveRuleEl.innerHTML = '+ Add';

                window.tempShapesToSave = null;
                window.shapes = [];
                window.tempShapes = [];

                if (window.DrawingTool) {
                    window.DrawingTool.loadShapesFromServer(null); // Xóa shapes hiện tại
                }
            })
        })

        /**
         * Xử lý UI khi người dùng click vào một rule type.
         * @param item
         */
        function handleRuleTypeClick(item = null) {
            // Xóa trạng thái active của các rule hiện có.
            ruleTypeEls.forEach(el => {
                el.classList.remove('bg-blue-500', 'text-white');
                el.classList.add('bg-white');
            });

            if (item) {
                // Thêm trạng thái active cho rule được chọn.
                item.classList.add('bg-blue-500', 'text-white');
                item.classList.remove('bg-white');
            } else {
                // Nếu không có item được truyền vào, chọn rule type đầu tiên.
                const firstRuleTypeEl = ruleTypeEls[0];
                firstRuleTypeEl.classList.add('bg-blue-500', 'text-white');
                firstRuleTypeEl.classList.remove('bg-white');
            }


        }

        /**
         * Hàm xử lý button khi người dùng nhấn vào
         * Có 2 trường hợp xảy ra là create vs update
         */
        function saveRule() {
            if (selectedAddedRule) {

            }
            const _action = selectedAddedRule ? 'updateInstanceRule' : 'createInstanceRule';
            const name = ruleNameEl.value.trim();
            const detected_object = Array.from(detectedObjectEl)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
            const rule_type = selectedRuleType;
            const rule_id = selectedAddedRule ? selectedAddedRule.id : null;
            const drawing_object = window.tempShapesToSave?.drawing_object
                || selectedAddedRule?.drawing_object || [];
            const direction = window.tempShapesToSave?.direction || selectedAddedRule.direction;

            if (drawing_object.length === 0) return;

            const requestData = {
                _action,
                name,
                detected_object,
                rule_type,
                rule_id,
                drawing_object,
                device_cvedixrt_instance_id: instanceId,
                direction
            }
            if (_action === 'updateInstanceRule') {
                requestData.rule_id = rule_id;
                requestData.uuid = selectedAddedRule.uuid;
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
                    if (response.ok) {
                        return response.json();
                    } else {
                        return response.json().then(errorData => {
                            throw new Error(errorData.message || 'Network response was not ok');
                        });
                    }
                })
                .then(data => {
                    if (data.success) {
                        // Cập nhật instanceRules
                        if (_action === 'createInstanceRule') {
                            instanceRules.push(data.data);
                        } else {
                            const index = instanceRules.findIndex(r => r.id === data.data.id);
                            if (index !== -1) {
                                instanceRules[index] = data.data;
                            }
                        }

                        // Show modal thông báo thành công
                        Swal.fire({
                            icon: 'success',
                            title: 'Thành công',
                            text: data.message,
                            timer: 1200,
                            showConfirmButton: false,
                        })
                            .then(() => {
                                window.shapes = [];
                                window.tempShapes = [];
                                window.tempShapesToSave = null;

                                // Cập nhật lại ui cho rule list.
                                updateUiRuleList(data.data);
                                handleRuleTypeClick();

                                // Reset selectedAddedRule
                                selectedAddedRule = null;
                                // reset btn save rule với giá trị hiển thị là  "+ Add"
                                btnSaveRuleEl.innerHTML = '+ Add';
                                // Reset rule name input
                                ruleNameEl.value = '';
                                // Reset detected object checkboxes
                                detectedObjectEl.forEach(checkbox => {
                                    checkbox.checked = false;
                                });
                                // Update

                                if (window.DrawingTool) {
                                    window.DrawingTool.loadShapesFromServer(null);
                                }
                            })
                        ;
                    } else {
                        // Hiển thị thông báo lỗi nếu có
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: data.message || 'Đã có lỗi xảy ra khi lưu rule.',
                            timer: 1200,
                            showConfirmButton: false,
                        });
                    }
                })
                .catch(error => {
                    console.error(error)
                    Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: 'Đã có lỗi xảy ra khi lưu rule.',
                        timer: 1200,
                        showConfirmButton: false,
                    });
                })
        }

        /**
         * Cập nhật UI rule list sau khi thêm hoặc cập nhật rule.
         * @param instanceRule
         */
        function updateUiRuleList(instanceRule) {
            let exitstingRuleItem = document.querySelector(`.rule-item[data-rule-id="${instanceRule.id}"]`);

            if (exitstingRuleItem) {
                exitstingRuleItem.textContent = instanceRule.name;
            } else {
                // Tạo mới rule item element
                let instanceRuleEl = document.createElement('div');
                instanceRuleEl.className = 'flex items-center justify-between';

                let liEl = document.createElement('li');
                liEl.className = 'rule-item px-2 py-1 rounded font-medium text-sm bg-white hover:bg-blue-100 cursor-pointer w-full overflow-hidden text-ellipsis whitespace-normal line-clamp-2';
                liEl.setAttribute('data-rule-id', instanceRule.id);
                liEl.textContent = instanceRule.name;
                liEl.addEventListener('click', function () {
                    handleRuleItemClick(this);

                    ruleItemEls.forEach(el => {
                        el.classList.remove('bg-blue-500', 'text-white');
                        el.classList.add('bg-white');
                    });

                    btnSaveRuleEl.innerText = 'Update +';

                    if (window.DrawingTool) {
                        window.DrawingTool.loadShapesFromServer(selectedAddedRule);
                    }

                });

                let btnDeleteRuleEl = document.createElement('button');
                btnDeleteRuleEl.type = 'button';
                btnDeleteRuleEl.setAttribute('data-rule-id', instanceRule.id);
                btnDeleteRuleEl.className = 'btn-delete-rule ml-2 px-2 py-1 rounded text-red-500 hover:text-red-700 hover:bg-blue-100';
                btnDeleteRuleEl.innerHTML = '&times;';
                btnDeleteRuleEl.setAttribute('onclick', `deleteRule(${instanceRule.id})`);

                instanceRuleEl.appendChild(liEl);
                instanceRuleEl.appendChild(btnDeleteRuleEl);
                ruleList.appendChild(instanceRuleEl);

                // Cập nhật ruleItemEls để bao gồm mục quy tắc mới
                ruleItemEls = document.querySelectorAll('.rule-item');
            }
        }

        /**
         * Xử lý sự kiện click vào nút xóa rule.
         */
        function deleteRule(ruleId) {
            Swal.fire({
                title: 'Xác nhận xóa',
                text: 'Bạn có chắc muốn xóa rule này?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Xóa',
                cancelButtonText: 'Hủy',
            })
                .then(result => {
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
                                if (response.ok) {
                                    return response.json();
                                }
                            })
                            .then(data => {
                                const parentRuleItemEl = document.querySelector(`.rule-item[data-rule-id="${ruleId}"]`)
                                    .parentElement;
                                if (parentRuleItemEl) {
                                    parentRuleItemEl.remove();
                                }
                                // Cập nhật lại instanceRules
                                instanceRules = instanceRules.filter(rule => rule.id !== ruleId);
                                // Reset selectedAddedRule
                                selectedAddedRule = null;
                                //// Reset form chuẩn bị cho create rule mới.
                                // Reset rule name
                                ruleNameEl.value = '';
                                // Reset detected object checkboxes
                                detectedObjectEl.forEach(checkbox => {
                                    checkbox.checked = false;
                                });
                                // Reset rule type
                                ruleTypeEls.forEach(rt => {
                                    rt.classList.remove('bg-blue-500', 'text-white');
                                    rt.classList.add('bg-white');
                                });
                                ruleTypeEls[0].classList.remove('bg-white');
                                ruleTypeEls[0].classList.add('bg-blue-500', 'text-white');

                                // Reset btn save rule với giá trị hiển thị là  "+ Add"
                                btnSaveRuleEl.innerHTML = '+ Add';
                                // Reset selectedAddedRule
                                selectedAddedRule = null;

                                // Modal thông báo xóa thành công
                                Swal.fire({
                                    title: 'Xóa thành công',
                                    text: 'Rule đã được xóa thành công.',
                                    icon: 'success',
                                    time: 1200,
                                });
                            })
                            .catch(error => {
                                //
                                Swal.fire({
                                    title: 'Lỗi',
                                    text: 'Đã có lỗi xảy ra khi xóa rule.',
                                    icon: 'error',
                                    time: 1200,
                                });
                            })
                    }
                })
        }

        document.addEventListener('DOMContentLoaded', function () {
            ruleTypeEls[0].classList.remove('bg-white');
            ruleTypeEls[0].classList.add('bg-blue-500', 'text-white');
            selectedRuleType = ruleTypeEls[0].getAttribute('data-rule-type');
        });
    </script>
@endpush
