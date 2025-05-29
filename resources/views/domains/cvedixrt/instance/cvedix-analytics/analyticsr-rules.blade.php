`@php
    use Illuminate\Support\Carbon;
    use App\Domains\Cvedixrt\Instance\Enums\DetectedObject;
    use App\Domains\Cvedixrt\Instance\Enums\RuleType;
@endphp
@extends('layouts.in')
{{--@dd($row->instanceRules)--}}
@section('body')
    <div class="flex rounded shadow bg-white min-h-[600px]">
        <!-- List Rule đã thêm -->
        <div class="w-1/3 flex flex-col bg-gray-50 border-r">
            <h3 class="px-4 py-3 font-semibold text-gray-800">{{ __('Added rules') }}</h3>
            <ul class="flex-1 py-4 px-2 space-y-1 overflow-y-auto max-h[32rem]">
                @foreach($row->instanceRules as $rule)
                    <div class="flex items-center justify-between">
                        <li data-rule-id="{{$rule->id}}"
                            class="rule-item block px-2 py-1 rounded font-medium text-sm
                        bg-white hover:bg-blue-100 focus:bg-blue-500 focus:text-white transition-colors cursor-pointer">
                            {{$rule->name}}

                        </li>
                        <button type="button" data-rule-id="{{$rule->id}}" class="btn-delete-rule ml-2 px-2 py-1
                        rounded text-red-500 hover:text-red-700 hover:bg-blue-100"
                                onclick="deleteRule({{ $rule->id }})">
                            &times;
                        </button>
                    </div>
                @endforeach
            </ul>

        </div>
        <!-- Sidebar: Rule Types -->
        <div class="w-1/3 bg-white flex flex-col">
            <h3 class="px-4 py-3 font-semibold">{{ __('cvedixt-analytic.analytics_rules') }}</h3>
            <div class="flex flex-col flex-1 space-y-2 ml-3 mt-3">
                @foreach(RuleType::cases() as $rule)
                    <a href="#"
                       class="btn form-control-lg mb-3 border-2 border-primary text-sm text-left justify-start bg-white hover:bg-blue-100 focus:bg-blue-500 focus:text-white transition-colors"
                       data-rule-type="{{ $rule }}">
                        {{ ucfirst($rule->value) }}
                    </a>
                @endforeach
            </div>

        </div>

        <!-- Rule Configuration -->
        <div class="w-full p-6">
            <div class="flex border border-gray-400 p-4">
                <!-- Left column: Rule name + Object types -->
                <div class="w-1/2 pr-4">
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-1">{{ __('cvedixt-analytic.rule_name') }}</label>
                        <input type="text" id="ruleNameInput" value="{{ old('name') }}"
                               class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div class="mb-4">
                        <div class="mb-2 font-medium">{{ __('cvedixt-analytic.object_detection') }}</div>
                        <div class="space-y-2">
                            @foreach(DetectedObject::cases() as $type)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="detect_objects" value="{{ $type->value }}"
                                           class="accent-blue-500">
                                    <span class="text-blue-600">{{ $type }}</span>
                                </label>
                            @endforeach
                        </div>
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
                <a href="#" onclick="openDrawingTool()" id="btn-save-rule"
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
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('/js/drawing-tool.js') }}"></script>
    <script>
        // Khởi tạo các biến toàn cục
        const instanceId = {{ $row->id ?? 'null' }};
        const instanceUuid = '{{ $row->uuid ?? '' }}';
        let selectedRuleType = 'Line Crossing';
        let selectedAddedRule = null; // Đại diện cho rule đã chọn (trong danh sách rule đã được thêm)
        const instanceRules = []; // Mảng chứa các rule đã thêm từ backend

        // Lấy danh sách các rule đã thêm từ backend
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


        function openDrawingTool() {
            if (!instanceId || !instanceUuid) {
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('cvedixt-analytic.alert_missing_instance') }}',
                });
                return;
            }

            // Lấy danh sách đối tượng phát hiện
            const checkboxes = document.querySelectorAll('input[name="detect_objects"]');
            const detectObjects = Array.from(checkboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);

            if (detectObjects.length === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __('cvedixt-analytic.alert_no_object') }}',
                });
                return;
            }

            // Lấy tên quy tắc
            const ruleName = document.getElementById('ruleNameInput').value.trim();
            if (!ruleName) {
                Swal.fire({
                    icon: 'warning',
                    title: '{{ __('cvedixt-analytic.alert_no_rule_name') }}',
                });
                return;
            }

            if (window.DrawingTool) {
                window.DrawingTool.open(instanceId, instanceUuid, {
                    rule_type: selectedRuleType,
                    detect_objects: detectObjects,
                    rule_name: ruleName
                });
            } else {
                console.error('{{ __('cvedixt-analytic.error_drawing_tool') }}');
                Swal.fire({
                    icon: 'error',
                    title: '{{ __('cvedixt-analytic.error_drawing_tool') }}'
                });
            }
        }

        document.querySelectorAll('.rule-item').forEach(item => {
            item.addEventListener('click', function () {
                let ruleId = parseInt(this.getAttribute('data-rule-id'), 10);

                selectedAddedRule = instanceRules.find(rule => rule.id === ruleId);

                // Xoá trạng thái active của các rule khác
                document.querySelectorAll('.rule-item').forEach(el => {
                    // console.log(el);
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
            });
        });

        /**
         * Cập nhật cấu hình rule dựa trên rule đã chọn
         *
         * @param {Object} dataCurrentRule - Dữ liệu của rule hiện tại
         *
         * @param dataCurrentRule
         */
        function updateRuleConfiguration(dataCurrentRule) {
            // console.log(dataCurrentRule.detected_object);
            // Cập nhật tên rule
            document.getElementById('ruleNameInput').value = dataCurrentRule?.name || '';

            // Cập nhật các checkbox của DetectedObject
            document.querySelectorAll('input[name="detect_objects"]').forEach(checkbox => {
                checkbox.checked = dataCurrentRule?.detected_object.includes(checkbox.value);
            });

            // Cập nhật rule type
            const buttons = document.querySelectorAll('.btn.form-control-lg');
            console.log('dataCurrentRule Type:', dataCurrentRule);
            buttons.forEach(btn => {
                console.log(btn.dataset.ruleType, dataCurrentRule?.rule_type);
                if (btn.dataset.ruleType === dataCurrentRule?.rule_type) {
                    btn.classList.remove('bg-white');
                    btn.classList.add('bg-blue-500', 'text-white');
                    selectedRuleType = dataCurrentRule?.rule_type;
                } else {
                    btn.classList.remove('bg-blue-500', 'text-white');
                    btn.classList.add('bg-white');
                }
            })
        }

        /**
         * Hàm xóa rule
         *
         * @param {number} ruleId - ID của rule cần xóa
         */
        function deleteRule(ruleId){
            console.log('Deleting rule with ID:', ruleId);

            // submit về domain hiện tại với _action : deleteInstanceRule
            // dùng hàm fetch
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
                    console.log('Success:', data);
                    // Xoá rule khỏi danh sách
                    const ruleItem = document.querySelector(`.rule-item[data-rule-id="${ruleId}"]`);
                    const btnDelteRule = document.querySelector(`.btn-delete-rule[data-rule-id="${ruleId}"]`);
                    if (ruleItem) {
                        ruleItem.remove();
                        btnDelteRule.remove();
                    }

                    // Reset selectedAddedRule và cập nhật cấu hình
                    selectedAddedRule = null;
                    updateRuleConfiguration(selectedAddedRule);

                    Swal.fire({
                        icon: 'success',
                        title: '{{ __('cvedixt-analytic.rule_deleted') }}',
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: '{{ __('cvedixt-analytic.error_deleting_rule') }}',
                    });
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const buttons = document.querySelectorAll('.btn.form-control-lg');
            if (buttons.length > 0) {
                buttons[buttons.length - 1].classList.remove('bg-white'); // Mặc định chọn Line Crossing
                buttons[buttons.length - 1].classList.add('bg-blue-500', 'text-white');
                selectedRuleType = buttons[buttons.length - 1].dataset.ruleType;
            }
            buttons.forEach(btn => {
                btn.addEventListener('click', e => {
                    e.preventDefault();
                    buttons.forEach(b => {
                        b.classList.remove('bg-blue-500', 'text-white');
                        b.classList.add('bg-white');

                        selectedAddedRule = null; // Reset rule đã chọn khi đổi loại rule
                        updateRuleConfiguration(selectedAddedRule)

                        // Xoá trạng thái active của các rule khác
                        document.querySelectorAll('.rule-item').forEach(el => {
                            // console.log(el);
                            el.classList.remove('bg-blue-500', 'text-white');
                            el.classList.add('bg-white');
                        });

                        const btnSaveRule = document.getElementById('btn-save-rule');
                        btnSaveRule.innerText = 'Add +';
                    });
                    btn.classList.remove('bg-white');
                    btn.classList.add('bg-blue-500', 'text-white');
                    selectedRuleType = btn.dataset.ruleType;
                });
            });
        });
    </script>
@endpush
`
