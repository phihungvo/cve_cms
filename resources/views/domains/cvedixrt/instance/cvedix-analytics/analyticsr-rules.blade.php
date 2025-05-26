@php use Illuminate\Support\Carbon; @endphp
@extends('layouts.in')

@section('body')
    <div class="flex h-[600px] rounded shadow bg-white">
        <!-- Sidebar: Rule Types -->
        <div class="w-1/3 bg-white flex flex-col">
            <h3 class="px-4 py-3 font-semibold">{{ __('cvedixt-analytic.analytics_rules') }}</h3>
            <div class="flex flex-col flex-1 space-y-2 ml-3 mt-3">
                @foreach(__('cvedixt-analytic.rule_types') as $rule)
                    <a href="#"
                       class="btn form-control-lg mb-3 border-2 border-primary text-sm text-left justify-start bg-white hover:bg-blue-100 focus:bg-blue-500 focus:text-white transition-colors"
                       data-rule-type="{{ $rule }}">
                        {{ $rule }}
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
                        <input type="text" id="ruleNameInput" value="{{ $row->name ?? '' }}"
                               class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-400">
                    </div>
                    <div class="mb-4">
                        <div class="mb-2 font-medium">{{ __('cvedixt-analytic.object_detection') }}</div>
                        <div class="space-y-2">
                            @foreach(__('cvedixt-analytic.object_types') as $type)
                                <label class="flex items-center space-x-2">
                                    <input type="checkbox" name="detect_objects" value="{{ $type }}"
                                           @if($type === 'Person') checked @endif class="accent-blue-500">
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
                <a href="#" onclick="openDrawingTool()" class="w-48 text-center px-12 py-2 border border-gray-500 rounded bg-white hover:bg-gray-100">
                    {{ __('cvedixt-analytic.add') }}
                </a>
            </div>
            <div class="flex justify-end space-x-4 mt-4">
                <a href="#"
                   class="w-32 text-center py-2 rounded bg-cyan-100 text-black border border-cyan-400 hover:bg-cyan-200">{{ __('cvedixt-analytic.ok') }}</a>
                <a href="#"
                   class="w-32 text-center py-2 rounded bg-cyan-100 text-black border border-cyan-400 hover:bg-cyan-200">{{ __('cvedixt-analytic.apply') }}</a>
                <a href="#" class="w-32 text-center py-2 rounded border border-gray-400 hover:bg-gray-100">{{ __('cvedixt-analytic.cancel') }}</a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="{{ asset('/js/drawing-tool.js') }}"></script>
    <script>
        const instanceId = {{ $row->id ?? 'null' }};
        const instanceUuid = '{{ $row->uuid ?? '' }}';
        let selectedRuleType = 'Line Crossing';

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
                    });
                    btn.classList.remove('bg-white');
                    btn.classList.add('bg-blue-500', 'text-white');
                    selectedRuleType = btn.dataset.ruleType;
                });
            });
        });
    </script>
@endpush
