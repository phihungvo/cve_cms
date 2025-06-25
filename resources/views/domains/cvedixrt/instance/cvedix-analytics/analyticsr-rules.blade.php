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
            <h3 class="px-4 py-3 font-semibold text-gray-800 mt-4">{{ __('cvedixt-analytic.added_rule') }}</h3>
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
            <div class="flex flex-col justify-between flex-1 ml-3 mb-2">
                @foreach(RuleType::cases() as $rule)
                    <a href="#"
                       class="btn form-control-lg py-3 border-2 border-primary text-sm text-left justify-start
                       bg-white hover:bg-blue-100 focus:text-white transition-colors cursor-pointer"
                       data-rule-type="{{ $rule->value }}">
                        {{ str_replace('_', ' ', ucfirst($rule->value)) }}
                    </a>
                @endforeach
            </div>
        </div>
        <!-- Rule Configuration -->
        <div id="ruleConfiguration" class="w-full p-6">
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
                        <div class="flex flex-col">
                            <div class="flex flex-col">
                                <!-- Person -->
                                <div x-data="{open: false}">
                                    <div class="flex items-center gap-2 ">
                                        <input type="checkbox" name="detect_objects" id="object_type_person"
                                               value="person"
                                               x-on:change="open = $event.target.checked">
                                        <label class="cursor-pointer flex-grow hover:font-bold p-2"
                                               for="object_type_person">Person</label>
                                    </div>
                                    <!-- Subtype for Person -->

                                    <div x-show="open" class="pl-6 flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" name="classification_object" value="prisoner"
                                                   id="object_type_prisoner">
                                            <label class="cursor-pointer flex-grow hover:font-bold p-2"
                                                   for="object_type_prisoner">Prisoner</label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" name="classification_object" value="wardener"
                                                   id="object_type_wardener">
                                            <label class="cursor-pointer flex-grow hover:font-bold p-2"
                                                   for="object_type_wardener">Wardener</label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" name="classification_object" value="policer"
                                                   id="object_type_policer">
                                            <label class="cursor-pointer flex-grow hover:font-bold p-2"
                                                   for="object_type_policer">Policer</label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" name="classification_object" value="laborer"
                                                   id="object_type_laborer">
                                            <label class="cursor-pointer flex-grow hover:font-bold p-2"
                                                   for="object_type_laborer">Laborer</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Vehicle -->
                                <div x-data="{open: false}">
                                    <div class="flex items-center gap-2 ">
                                        <input type="checkbox" name="detect_objects" id="object_type_vehicle"
                                               value="vehicle"
                                               x-on:change="open = $event.target.checked">
                                        <label class="cursor-pointer flex-grow hover:font-bold p-2"
                                               for="object_type_vehicle">Vehicle</label>
                                    </div>
                                    <!-- Subtypes for Vehicle -->
                                    <div x-show="open" class="pl-6 flex flex-col">
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" name="classification_object" value="car"
                                                   id="object_type_car">
                                            <label class="cursor-pointer flex-grow hover:font-bold p-2"
                                                   for="object_type_car">Car</label>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <input type="checkbox" name="classification_object" value="truck"
                                                   id="object_type_truck">
                                            <label class="cursor-pointer flex-grow hover:font-bold p-2"
                                                   for="object_type_truck">Truck</label>
                                        </div>
                                    </div>
                                </div>
                                <!-- Animal -->
                                <div class="flex items-center gap-2 ">
                                    <input type="checkbox" name="detect_objects" id="object_type_animal" value="animal">
                                    <label class="cursor-pointer flex-grow hover:font-bold p-2"
                                           for="object_type_animal">Animal</label>
                                </div>
                                <!-- Unknown -->
                                <div class="flex items-center gap-2 ">
                                    <input type="checkbox" name="detect_objects" id="object_type_unknown"
                                           value="unknown">
                                    <label class="cursor-pointer flex-grow hover:font-bold p-2"
                                           for="object_type_unknown">Unknown</label>
                                </div>
                            </div>
                        </div>
                        <div id="detectObjectsError"
                             class="text-red-500 text-sm mt-1 hidden">{{ __('cvedixt-analytic.alert_no_object') }}</div>
                    </div>
                    <!-- input range for priority -->
                    <div class="flex justify-start items-center gap-2 mt-3">
                        <label class="font-bold" for="priority">priority:</label>
                        <input class="w-1/2" type="range" id="priority" name="priority" min="1" max="5"
                               value="1" oninput="priorityOutput.value = priority.value"
                        >
                        <output id="priorityOutput">1</output>
                    </div>
                </div>
                <!-- Right column: Camera live -->
                <div class="w-1/2 pl-4 flex flex-col">
                    <h3 class="text-sm font-bold mb-1">{{ __('rt-analytics-rules.live-view-camera') }}</h3>
                    <div
                        class="border border-gray-300 rounded bg-white h-40 w-full flex items-center justify-center text-black relative overflow-hidden flex-grow">
                        <div id="videoWrapperOutside" class="w-full h-full relative">
                            <video id="videoElementOutside"
                                   class="absolute top-0 w-full h-full object-contain"
                                   style="z-index: 0;" autoplay loop muted playsinline>
                                <source
                                    src="http://commondatastorage.googleapis.com/gtv-videos-bucket/sample/TearsOfSteel.mp4"
                                    type="video/mp4">
                            </video>
                            <canvas id="canvasOverlayOutside" class="absolute inset-0 h-full"
                                    style="z-index: 10; pointer-events: none;"></canvas>
                        </div>
                    </div>
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
            <div class="flex justify-center mt-4">
                <a href="#" onclick="saveRule()" id="btn-save-rule"
                   class="w-48 text-center px-12 py-2 border border-gray-500 rounded bg-white hover:bg-gray-100">
                    {{ __('cvedixt-analytic.add') }}
                </a>
            </div>
        </div>

        <div id="generalSetting" class="w-full p-6 hidden">
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
        @push('styles')
            <style>
                input[type="range"]::-webkit-slider-runnable-track {
                    background: linear-gradient(to right, #05FF03, #96FF03, #FFF601, #FE9401, #FD0300);
                    border-radius: 10px;
                }

                input[type="range"]::-webkit-slider-runnable-track {
                    background: linear-gradient(to right, #05FF03, #96FF03, #FFF601, #FE9401, #FD0300);
                    border-radius: 10px;
                }

                input[type="range"]::-webkit-slider-runnable-track {
                    background: linear-gradient(to right, #05FF03, #96FF03, #FFF601, #FE9401, #FD0300);
                    border-radius: 10px;
                }
            </style>
        @endpush
        @push('scripts')
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
            <script src="{{ asset('/js/drawing-tool.js') }}"></script>
            <script>
                const instanceId = {{ $row->id }};
                const instanceUuid = '{{ $row->uuid }}';
                let selectedRuleType = 'line_crossing';
                let selectedAddedRule = null;
                let instanceRules = [];

                let priorityEl = document.getElementById('priority');
                let priorityOutputEL = document.getElementById('priorityOutput');

                const ruleList = document.getElementById('ruleList');
                const ruleNameEl = document.getElementById('ruleNameInput');
                const detectObjectsCheckboxes = document.querySelectorAll('input[name="detect_objects"]');
                const classificationObjectsCheckboxes = document.querySelectorAll('input[name="classification_object"]');

                const ruleTypeButtons = document.querySelectorAll('.btn.form-control-lg');
                let ruleItemsEl = document.querySelectorAll('.rule-item');
                const btnSaveRule = document.getElementById('btn-save-rule');

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
                        priority: {{ $rule->priority }},
                    });
                    @endforeach
                }

                let videoWrapperOutside, videoElementOutside, canvasOverlayOutside, ctxOutside;
                const SHAPE_SCALE_FACTOR = 1.2; // Tăng kích thước shape lên 1.2 lần

                /**
                 * Resize canvas khớp kích thước video ngoài và vẽ lại shapes cho rule đã chọn.
                 * - Nếu thiếu element hoặc video chưa sẵn sàng thì thử lại.
                 * - Tính tỉ lệ scale, đặt lại kích thước và vị trí canvas.
                 * - Vẽ shapes nếu có, ngược lại clear canvas.
                 */
                function resizeCanvasWithRuleId(ruleId) {
                    videoWrapperOutside = document.getElementById('videoWrapperOutside');
                    videoElementOutside = document.getElementById('videoElementOutside');
                    canvasOverlayOutside = document.getElementById('canvasOverlayOutside');
                    ctxOutside = canvasOverlayOutside.getContext('2d');

                    if (!videoWrapperOutside || !videoElementOutside || !canvasOverlayOutside) {
                        console.warn('Missing required elements for canvas resize');
                        setTimeout(() => resizeCanvasWithRuleId(ruleId), 200);
                        return false;
                    }

                    if (!videoElementOutside.videoWidth || !videoElementOutside.videoHeight) {
                        videoElementOutside.addEventListener('loadedmetadata', () => resizeCanvasWithRuleId(ruleId), {once: true});
                        return false;
                    }

                    // Lấy kích thước wrapper và video gốc
                    const wrapperWidth = videoWrapperOutside.offsetWidth;
                    const wrapperHeight = videoWrapperOutside.offsetHeight;
                    const videoWidth = videoElementOutside.videoWidth;
                    const videoHeight = videoElementOutside.videoHeight;

                    if (wrapperWidth === 0 || wrapperHeight === 0) {
                        console.warn('Wrapper dimensions are zero, retrying...');
                        setTimeout(() => resizeCanvasWithRuleId(ruleId), 200);
                        return false;
                    }

                    // Tính tỷ lệ scale giống như trong DrawingTool
                    const scale = Math.min(wrapperWidth / videoWidth, wrapperHeight / videoHeight);
                    const scaledWidth = videoWidth * scale;
                    const scaledHeight = videoHeight * scale;

                    // Đặt kích thước canvas khớp với kích thước video hiển thị
                    canvasOverlayOutside.width = scaledWidth;
                    canvasOverlayOutside.height = scaledHeight;
                    canvasOverlayOutside.style.width = `${scaledWidth}px`;
                    canvasOverlayOutside.style.height = `${scaledHeight}px`;

                    // Đặt kích thước và vị trí video
                    videoElementOutside.style.width = `${scaledWidth}px`;
                    videoElementOutside.style.height = `${scaledHeight}px`;

                    // Căn giữa video và canvas trong wrapper
                    const offsetX = (wrapperWidth - scaledWidth) / 2;
                    const offsetY = (wrapperHeight - scaledHeight) / 2; // Giảm 10px để căn chỉnh với viền
                    videoElementOutside.style.position = 'absolute';
                    videoElementOutside.style.left = `${offsetX}px`;
                    videoElementOutside.style.top = `${offsetY}px`;
                    canvasOverlayOutside.style.position = 'absolute';
                    canvasOverlayOutside.style.left = `${offsetX}px`;
                    canvasOverlayOutside.style.top = `${offsetY}px`;

                    // Vẽ lại shapes với scale và offset
                    selectedAddedRule = instanceRules.find(rule => rule.id === ruleId);
                    if (selectedAddedRule && selectedAddedRule.drawing_object) {
                        drawShapesOnOutsideCanvas(selectedAddedRule.drawing_object, scale, offsetX, offsetY);
                    } else {
                        ctxOutside.clearRect(0, 0, canvasOverlayOutside.width, canvasOverlayOutside.height);
                    }

                    return true;
                }

                /**
                 * Vẽ các shape (line, rect, poly) lên canvas ngoài theo tỉ lệ.
                 * @param {Array} drawingObjects - Danh sách shape để vẽ.
                 * @param {number} scale - Tỉ lệ khớp với video.
                 */
                function drawShapesOnOutsideCanvas(drawingObjects, scale, offsetX, offsetY) {
                    ctxOutside.clearRect(0, 0, canvasOverlayOutside.width, canvasOverlayOutside.height);
                    if (!drawingObjects || !Array.isArray(drawingObjects)) return;

                    drawingObjects.forEach(shape => {
                        ctxOutside.beginPath();
                        ctxOutside.strokeStyle = rgbToHex(shape.color || [255, 0, 0]);
                        ctxOutside.fillStyle = rgbToRgba(shape.color || [255, 0, 0], 0.12);
                        ctxOutside.lineWidth = 1; // Tăng độ dày đường viền

                        if (shape.type === 'line') {
                            const rotation = shape.rotation || 0;
                            const midX = (shape.startX + shape.endX) / 2;
                            const midY = (shape.startY + shape.endY) / 2;

                            ctxOutside.save();
                            ctxOutside.translate((midX * scale * SHAPE_SCALE_FACTOR)
                                + offsetX, (midY * scale * SHAPE_SCALE_FACTOR) + offsetY);
                            ctxOutside.rotate(rotation);
                            ctxOutside.moveTo((shape.startX - midX) * scale * SHAPE_SCALE_FACTOR,
                                (shape.startY - midY) * scale * SHAPE_SCALE_FACTOR);
                            ctxOutside.lineTo((shape.endX - midX) * scale * SHAPE_SCALE_FACTOR,
                                (shape.endY - midY) * scale * SHAPE_SCALE_FACTOR);
                            ctxOutside.stroke();
                            ctxOutside.restore();
                        } else if (shape.type === 'rect') {
                            ctxOutside.rect(
                                (shape.startX * scale * SHAPE_SCALE_FACTOR) + offsetX,
                                (shape.startY * scale * SHAPE_SCALE_FACTOR) + offsetY,
                                shape.width * scale * SHAPE_SCALE_FACTOR,
                                shape.height * scale * SHAPE_SCALE_FACTOR
                            );
                            ctxOutside.fill();
                            ctxOutside.stroke();
                        } else if (shape.type === 'poly' && shape.points && shape.points.length > 0) {
                            ctxOutside.moveTo((shape.points[0].x * scale * SHAPE_SCALE_FACTOR)
                                + offsetX, (shape.points[0].y * scale * SHAPE_SCALE_FACTOR) + offsetY);
                            shape.points.slice(1).forEach(point => ctxOutside.lineTo((point.x * scale * SHAPE_SCALE_FACTOR)
                                + offsetX, (point.y * scale * SHAPE_SCALE_FACTOR) + offsetY));
                            ctxOutside.closePath();
                            ctxOutside.fill();
                            ctxOutside.stroke();
                        }
                    });
                }

                /**
                 * Chuyển một mảng RGB \[r, g, b\] thành chuỗi màu hex (ví dụ: "#ff0000").
                 * Trả về "#000000" nếu đầu vào không hợp lệ.
                 * @param {number[]} rgb - Mảng gồm 3 số đại diện cho giá trị RGB.
                 * @returns {string} Chuỗi màu hex.
                 */
                function rgbToHex(rgb) {
                    if (!Array.isArray(rgb) || rgb.length !== 3 || rgb.some(x => typeof x !== 'number')) {
                        console.error('Invalid RGB input:', rgb);
                        return '#000000';
                    }
                    return '#' + rgb.map(x => {
                        const hex = x.toString(16);
                        return hex.length === 1 ? '0' + hex : hex;
                    }).join('');
                }

                /**
                 * Chuyển một mảng RGB \[r, g, b\] và giá trị alpha thành chuỗi màu rgba() CSS.
                 * Trả về 'rgba(0,0,0,0.2)' nếu đầu vào không hợp lệ.
                 * @param {number[]} rgb - Mảng gồm 3 số đại diện cho giá trị RGB.
                 * @param {number} [alpha=0.2] - Giá trị alpha cho độ trong suốt.
                 * @returns {string} Chuỗi màu RGBA.
                 */
                function rgbToRgba(rgb, alpha = 0.2) {
                    if (!Array.isArray(rgb) || rgb.length !== 3 || rgb.some(x => typeof x !== 'number')) {
                        console.error('Invalid RGB input for RGBA:', rgb);
                        return 'rgba(0,0,0,0.2)';
                    }
                    return `rgba(${rgb[0]},${rgb[1]},${rgb[2]},${alpha})`;
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

                    btnSaveRule.innerText = 'Update +';

                    // Cập nhật form cấu hình
                    updateRuleConfiguration(selectedAddedRule);

                    if (selectedAddedRule && window.DrawingTool) {
                        window.DrawingTool.loadShapesFromServer(selectedAddedRule);
                        resizeCanvasWithRuleId(ruleId); // Gọi hàm để resize và vẽ shapes
                    }
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
                        priority: addedRule.priority
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
                    // Lấy dữ liệu detected_object theo mẫu yêu cầu
                    const detectObjects = Array.from(document.querySelectorAll('input[name="detect_objects"]:checked')).map(cb => {
                        const detected_object = cb.value;
                        let classification_object = [];

                        // Lấy các checkbox classification_object liên quan nếu có
                        if (detected_object === 'person') {
                            classification_object = Array.from(document.querySelectorAll('input[name="classification_object"]:checked'))
                                .filter(subCb => ['prisoner', 'wardener', 'policer', 'laborer'].includes(subCb.value))
                                .map(subCb => subCb.value);
                        } else if (detected_object === 'vehicle') {
                            classification_object = Array.from(document.querySelectorAll('input[name="classification_object"]:checked'))
                                .filter(subCb => ['car', 'truck'].includes(subCb.value))
                                .map(subCb => subCb.value);
                        }

                        return {
                            detected_object,
                            classification_object
                        };
                    });
                    // detected_object: [
                    //     {
                    //         "detected_object": "person",
                    //         "classification_object": ["prisoner", "wardener"]
                    //     },
                    //     {
                    //         "detected_object": "vehicle",
                    //         "classification_object": ["car", "truck"]
                    //     }
                    // ];
                    const ruleType = selectedRuleType;
                    const ruleId = selectedAddedRule ? selectedAddedRule.id : null;
                    const uuid = window.tempShapesToSave?.uuid || selectedAddedRule?.uuid || instanceUuid;
                    const drawingObject = window.tempShapesToSave?.drawing_object
                        || selectedAddedRule?.drawing_object || [];
                    const direction = window.tempShapesToSave?.direction || selectedAddedRule.direction;
                    const priority = priorityEl.value

                    const requestData = {
                        _action: action,
                        uuid: uuid,
                        name: ruleName,
                        detected_object: detectObjects,
                        rule_type: ruleType,
                        drawing_object: drawingObject,
                        direction: direction,
                        cvedixrt_instance_id: instanceId,
                        priority: priority
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

                                    btnSaveRule.innerText = 'Add +';

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
                            const isChecked = Array.isArray(dataCurrentRule.detected_object) &&
                                dataCurrentRule.detected_object.some(obj => obj.detected_object === checkbox.value);
                            checkbox.checked = isChecked;

                            if (checkbox.closest('[x-data]') && checkbox.closest('[x-data]')._x_dataStack[0]) {
                                checkbox.closest('[x-data]')._x_dataStack[0].open = isChecked;
                            }
                        });
                        classificationObjectsCheckboxes.forEach(checkbox => {
                            checkbox.checked = dataCurrentRule.detected_object.some(obj => obj.classification_object.includes(checkbox.value));
                        })
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
                        priorityEl.value = dataCurrentRule.priority;
                        priorityOutputEL.value = dataCurrentRule.priority;
                    } else {
                        ruleNameEl.value = '';
                        // Reset các checkbox của DetectedObject
                        detectObjectsCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        });

                        classificationObjectsCheckboxes.forEach(checkbox => {
                            checkbox.checked = false;
                        })

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

                        resizeCanvasWithRuleId(null);

                        priorityEl.value = 1
                        priorityOutputEL.value = 1;


                        // // Reset video canvas
                        // if (videoWrapperOutside && videoElementOutside && canvasOverlayOutside) {
                        //     ctxOutside.clearRect(0, 0, canvasOverlayOutside.width, canvasOverlayOutside.height);
                        // }
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
                                .then(async response => {
                                    if (!response.ok) {
                                        const data = await response.json();
                                        throw new Error(data.message || 'Không thể xóa rule');
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

                                    btnSaveRule.innerText = 'Add +';

                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Thành công',
                                        text: 'Đã xóa rule',
                                        timer: 1200,
                                        showConfirmButton: false
                                    });
                                })
                                .catch(error => {
                                    Swal.fire({
                                        icon: 'error',
                                        title: "Delete Rule Error",
                                        text: error.message,
                                        timer: 1200,
                                        showConfirmButton: false
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

                            if (selectedAddedRule) {
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
                                // Xóa shapes trong canvas
                                window.shapes = [];
                                window.tempShapes = [];
                                window.tempShapesToSave = null;

                                // Reset và xóa canvas ngoài
                                if (ctxOutside && canvasOverlayOutside) {
                                    ctxOutside.clearRect(0, 0, canvasOverlayOutside.width, canvasOverlayOutside.height);
                                }

                                priorityEl.value = 1;
                                priorityOutputEL.value = 1;

                                classificationObjectsCheckboxes.forEach(checkbox => {
                                    checkbox.checked = false;
                                    if (checkbox.closest('[x-data]') && checkbox.closest('[x-data]')._x_dataStack[0]) {
                                        checkbox.closest('[x-data]')._x_dataStack[0].open = false;
                                    }
                                })
                            }

                            // Reset trạng thái rule đã chọn
                            selectedAddedRule = null;

                            btnSaveRule.innerText = 'Add +';

                            // if (window.DrawingTool) {
                            //     window.DrawingTool.loadShapesFromServer(null);
                            // }
                            resizeCanvasWithRuleId(null);
                        });
                    });
                });
            </script>
    @endpush
