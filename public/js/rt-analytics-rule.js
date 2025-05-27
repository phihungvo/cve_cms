function showUiDraw() {
    let cameraStream = null;
    let canvasInstance = null;

    function generateUUID() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            const r = (crypto.getRandomValues(new Uint8Array(1))[0] % 16) | 0;
            const v = c === 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }

    function rgbToHex(rgb) {
        return `#${rgb.map(x => Math.round(x).toString(16).padStart(2, '0')).join('')}`;
    }

    function hexToRgb(hex) {
        const result = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec(hex);
        return result ? [parseInt(result[1], 16), parseInt(result[2], 16), parseInt(result[3], 16)] : [0, 0, 0];
    }

    const swalWithBootstrapButtons = Swal.mixin({
        customClass: {
            confirmButton: 'btn btn-primary',
            cancelButton: 'btn btn-danger'
        },
        buttonsStyling: false
    });

    swalWithBootstrapButtons.fire({
        title: 'Canvas Draw',
        html: `
<div class="custom-canvas-container flex flex-col justify-center items-center">
    <div class="flex w-full" style="height: 450px;">
        <div style="position: relative; width: 640px; height: 480px;">
            <video id="videoStream" autoplay muted playsinline
                style="position: absolute; top: 0; left: 0; z-index: 1;">
                Your browser does not support the video tag.
            </video>
            <canvas id="drawingCanvas" width="640" height="480"
                style="position: absolute; top: 0; left: 0; z-index: 2; border: 1px solid #000; background: transparent;">
            </canvas>
        </div>
        <div id="lineInfoPanel" class="w-1/4 p-4 overflow-y-auto border-l border-gray-300" style="height: 100%;">
            <h3 class="text-lg font-medium mb-2">Line Information</h3>
            <ul id="lineInfoList" class="flex flex-col gap-2"></ul>
        </div>
    </div>
</div>
            `,
        showCloseButton: true,
        confirmButtonText: 'OK',
        width: '60%',
        showCancelButton: true,
        cancelButtonText: 'Cancel',

        didOpen: () => {
            const container = Swal.getHtmlContainer();

            const controls = document.createElement('div');
            controls.innerHTML = `
        <div style="text-align: center; margin-bottom: 10px;">
            <button id="drawModeBtn" class="btn btn-outline-primary">✏️ Vẽ</button>
            <button id="selectModeBtn" class="btn btn-outline-secondary">🖱️ Chỉnh sửa</button>
            <input type="color" id="colorPicker" value="#000000">
        </div>
    `;
            container.insertBefore(controls, container.firstChild);

            const video = document.getElementById('videoStream');
            const canvasEl = document.getElementById('drawingCanvas');
            canvasInstance = new fabric.Canvas(canvasEl);

            console.log('Canvas Logical Size (HTML):', {width: canvasEl.width, height: canvasEl.height});
            console.log('Canvas Display Size (CSS):', {width: canvasEl.offsetWidth, height: canvasEl.offsetHeight});

            let mode = 'draw';
            let isDrawing = false;

            function loadCanvasData() {
                let processedCanvasData = null;
                const canvasWidth = canvasInstance.width; // 640
                const canvasHeight = canvasInstance.height; // 480

                console.log('Canvas size when loading:', {width: canvasWidth, height: canvasHeight});

                if (initialCanvasData && Array.isArray(initialCanvasData)) {
                    const normalizedObjects = initialCanvasData.map(obj => {
                        if (obj.type === 'line') {
                            if (obj.x1 === undefined || obj.y1 === undefined ||
                                obj.x2 === undefined || obj.y2 === undefined) {
                                console.error('Invalid coordinates format:', obj);
                                return null;
                            }

                            const stroke = obj.stroke || rgbToHex(obj.color || [0, 0, 0]);
                            return {
                                type: 'line',
                                x1: obj.x1,
                                y1: obj.y1,
                                x2: obj.x2,
                                y2: obj.y2,
                                stroke: stroke,
                                strokeWidth: obj.strokeWidth || 4,
                                id: obj.id || generateUUID(),
                                label: obj.label || 'Unnamed',
                                classes: obj.classes || [],
                                direction: obj.direction || 'Unknown'
                            };
                        }
                        return obj;
                    }).filter(obj => obj !== null);

                    processedCanvasData = {
                        version: '5.1.0',
                        objects: normalizedObjects
                    };
                } else {
                    processedCanvasData = {
                        version: '5.1.0',
                        objects: []
                    };
                }

                if (processedCanvasData.objects.length > 0) {
                    try {
                        console.log('Processed Canvas Data before load:', JSON.stringify(processedCanvasData, null, 2));
                        canvasInstance.loadFromJSON(processedCanvasData, () => {
                            const lines = {};
                            canvasInstance.forEachObject(obj => {
                                console.log('Object loaded - Position:', {
                                    x1: obj.x1,
                                    y1: obj.y1,
                                    x2: obj.x2,
                                    y2: obj.y2
                                });
                                obj.selectable = false;
                                if (obj.id) {
                                    // Thêm nhãn cho line đã load
                                    const dx = obj.x2 - obj.x1;
                                    const dy = obj.y2 - obj.y1;
                                    const angle = Math.atan2(dy, dx)*180/Math.PI;

                                    let left, top;
                                    if (Math.abs(angle) < 45 || Math.abs(angle) > 135) { // Đường ngang
                                        left = (obj.x1 + obj.x2) / 2;
                                        top = Math.min(obj.y1, obj.y2) - 20;
                                    } else { // Đường dọc hoặc chéo
                                        left = Math.max(obj.x1, obj.x2) + 10;
                                        top = (obj.y1 + obj.y2) / 2;
                                    }

                                    const labelText = new fabric.Text(obj.label || 'Unnamed', {
                                        left: left,
                                        top: top,
                                        fontSize: 14,
                                        fill: '#000000',
                                        selectable: false,
                                        id: obj.id
                                    });
                                    canvasInstance.add(labelText);
                                    // Thêm thông tin vào panel
                                    addLineInfo(obj, obj.label);
                                }
                            });

                            console.log('Grouped lines:', lines);

                            Object.values(lines).forEach(group => {
                                if (group.line) {
                                    addLineInfo(group.line, group.line.label);
                                }
                            });

                            canvasInstance.renderAll();
                        }, (error) => {
                            console.error('Error loading canvas data:', error);
                            console.error('Invalid JSON data:', processedCanvasData);
                        });
                    } catch (error) {
                        console.error('Exception during loadFromJSON:', error);
                        console.error('Invalid JSON data:', processedCanvasData);
                    }
                }
            }

            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({
                    // video: true,
                    audio: false,
                    video: {width: 640, height: 480}
                })
                    .then(stream => {
                        cameraStream = stream;
                        video.srcObject = stream;
                        video.play().catch(err => console.error('Video play error:', err));

                        video.addEventListener('loadedmetadata', () => {
                            console.log('Video loadedmetadata - Width:', video.videoWidth, 'Height:', video.videoHeight);
                            loadCanvasData();
                            canvasInstance.renderAll();
                        });
                    })
                    .catch(err => {
                        console.error('Error accessing camera:', err);
                        Swal.fire({
                            icon: 'error',
                            title: 'Lỗi',
                            text: 'Không thể truy cập camera. Vui lòng kiểm tra quyền truy cập hoặc thiết bị camera.'
                        });
                    });
            } else {
                console.error('getUserMedia not supported in this browser');
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Trình duyệt của bạn không hỗ trợ truy cập camera.'
                });
            }

            function updateLabelOnCanvas (id, newLabel, line) {
                if(!id || !newLabel) {
                    console.warn('Invalid id or newLabel:', { id, newLabel });
                    return;
                }
                 const labelObjects = canvasInstance.getObjects().filter(obj => obj.type === 'text' && obj.id === id);
                if(labelObjects && labelObjects.length > 0 && labelObjects[0]) {
                    const dx = line.x2 - line.x1;
                    const dy = line.y2 - line.y1;
                    const length = Math.sqrt(dx * dx + dy * dy);
                    const angle = Math.atan2(dy, dx) * 180 / Math.PI; // Góc của đường (độ)

                    let left, top;
                    if (Math.abs(angle) < 45 || Math.abs(angle) > 135) { // Đường ngang
                        left = (line.x1 + line.x2) / 2;
                        top = Math.min(line.y1, line.y2) - 20; // Phía trên 20px
                    } else { // Đường dọc hoặc chéo
                        left = Math.max(line.x1, line.x2) + 10; // Bên phải 10px
                        top = (line.y1 + line.y2) / 2; // Trung điểm y
                    }

                    labelObjects[0].set({
                        text: newLabel,
                        left: left,
                        top: top,
                        fontSize: 14,
                        fill: '#000000'
                    });
                    canvasInstance.renderAll();
                }else{
                    console.warn('No label object found for id:', id);
                }
            }

            function addLineInfo(line, label = 'Unnamed') {
                const lineInfoList = document.getElementById('lineInfoList');
                const length = Math.sqrt(
                    Math.pow(line.x2 - line.x1, 2) + Math.pow(line.y2 - line.y1, 2)
                ).toFixed(2);

                if (!line.id) {
                    line.id = Date.now() + Math.random();
                }

                const lineInfo = document.createElement('li');
                lineInfo.className = 'p-2 border rounded bg-gray-100';
                lineInfo.dataset.lineId = line.id;
                lineInfo.innerHTML = `
                        <div>
                            <label>Name</label>
                            <input value="${label}" data-line-id="${line.id}" class="line-label-input"/>
                        </div>
                        <div>
                            <strong>Color:</strong>
                            <input type="color" value="${line.stroke || '#000000'}" data-line-id="${line.id}" class="line-color-input">
                        </div>
                        <div class="length-info"><strong>Length:</strong> ${length}px</div>
                        <button class="delete-line-btn btn btn-danger mt-2" data-line-id="${line.id}">Delete</button>
                    `;
                lineInfoList.appendChild(lineInfo);

                const labelInput = lineInfo.querySelector('.line-label-input');
                labelInput.addEventListener('change', (e) => {
                    const newLabel = e.target.value.trim();
                    if (newLabel) {
                        line.label = newLabel;
                        updateLineInfo(line);
                        updateLabelOnCanvas(line.id, newLabel, line); // Cập nhật nhãn trên canvas
                    }
                });

                const colorInput = lineInfo.querySelector('.line-color-input');
                colorInput.addEventListener('input', (e) => {
                    line.set({stroke: e.target.value});
                    canvasInstance.renderAll();
                    updateLineInfo(line);
                });

                const deleteBtn = lineInfo.querySelector('.delete-line-btn');
                deleteBtn.addEventListener('click', (e) => {
                    // Xoá line và label trên canvas
                    canvasInstance.remove(line);
                    const labelObjects = canvasInstance.getObjects().filter(obj => obj.type === 'text' && obj.id === line.id);
                    labelObjects.forEach(label => canvasInstance.remove(label));
                    // Xoá thông tin khởi panel
                    removeLineInfo(line);
                    canvasInstance.renderAll();
                });
            }

            function updateLineInfo(line) {
                const lineInfo = document.getElementById('lineInfoList')
                    .querySelector(`li[data-line-id="${line.id}"]`);
                if (lineInfo) {
                    const labelInput = lineInfo.querySelector('.line-label-input');
                    const colorInput = lineInfo.querySelector('.line-color-input');
                    const lengthDiv = lineInfo.querySelector('.line-info');
                    const length = Math.sqrt(
                        Math.pow(line.x2 - line.x1, 2) + Math.pow(line.y2 - line.y1, 2)
                    ).toFixed(2);
                    labelInput.value = line.label || 'Unnamed';
                    colorInput.value = line.stroke || '#000000';
                    if (lengthDiv) {
                        lengthDiv.textContent = `Length: ${length}px`; // Cập nhật nội dung của length-info
                    } else {
                        console.error('Length div not found for line:', line.id);
                    }
                }
            }

            function removeLineInfo(line) {
                const lineInfoList = document.getElementById('lineInfoList');
                const lineInfo = lineInfoList.querySelector(`li[data-line-id="${line.id}"]`);
                if (lineInfo) {
                    lineInfoList.removeChild(lineInfo);
                }
            }

            document.getElementById('drawModeBtn').addEventListener('click', () => {
                mode = 'draw';
                canvasInstance.selection = false;
                canvasInstance.discardActiveObject().renderAll();
                canvasInstance.forEachObject(obj => {
                    if (obj.type !== 'textbox') {
                        obj.selectable = false;
                    }
                });
            });

            document.getElementById('selectModeBtn').addEventListener('click', () => {
                mode = 'select';
                canvasInstance.selection = true;
                canvasInstance.forEachObject(obj => {
                    if (obj.type !== 'textbox') {
                        obj.selectable = true;
                    }
                });
                canvasInstance.renderAll();
            });

            canvasInstance.on('mouse:down', (options) => {
                if (mode !== 'draw') return;
                isDrawing = true;
                const pointer = canvasInstance.getPointer(options.e);
                console.log('Mouse Down - Pointer:', {x: pointer.x, y: pointer.y});
                const id = generateUUID();
                const line = new fabric.Line([pointer.x, pointer.y, pointer.x, pointer.y], {
                    stroke: document.getElementById('colorPicker').value,
                    strokeWidth: 4,
                    selectable: true,
                    id: id,
                    label: `Line ${canvasInstance.getObjects('line').length + 1}`,
                    classes: [],
                    direction: 'Unknown'
                });
                console.log('Line Created - Initial:', {x1: line.x1, y1: line.y1, x2: line.x2, y2: line.y2});
                canvasInstance.add(line);
                canvasInstance._currentLine = line;
                addLineInfo(line, line.label);
            });

            canvasInstance.on('mouse:up', (options) => {
                if (mode !== 'draw' || !isDrawing) return;
                isDrawing = false;

                const line = canvasInstance._currentLine;
                canvasInstance._currentLine = null;

                if (!line) return;

                console.log('Mouse Up - Final Line:', {x1: line.x1, y1: line.y1, x2: line.x2, y2: line.y2});

                const isLineTooShort = line.x1 === line.x2 && line.y1 === line.y2;
                if (isLineTooShort) {
                    console.log('Line too short, removing:', line);
                    canvasInstance.remove(line);
                    removeLineInfo(line);
                    // Xoá nhãn nếu có.
                    const labelObject = canvasInstance.getObjects().filter(obj => obj.type === 'text' && obj.id === line.id);
                    labelObject.forEach(label => canvasInstance.remove(label));
                    return;
                }

                const dx = line.x2 - line.x1;
                const dy = line.y2 - line.y1;
                const length = Math.sqrt(dx * dx + dy * dy);
                const angle = Math.atan2(dy, dx) * 180 / Math.PI;

                let left, top;
                if (Math.abs(angle) < 45 || Math.abs(angle) > 135) { // Đường ngang
                    left = (line.x1 + line.x2) / 2;
                    top = Math.min(line.y1, line.y2) - 20;
                } else { // Đường dọc hoặc chéo
                    left = Math.max(line.x1, line.x2) + 10;
                    top = (line.y1 + line.y2) / 2;
                }

                const labelText = new fabric.Text(line.label, {
                    left: left,
                    top: top,
                    fontSize: 14,
                    fill: '#000000',
                    selectable: false,
                    id: line.id
                });
                canvasInstance.add(labelText);
                canvasInstance.renderAll();
            });

            canvasInstance.on('mouse:move', (options) => {
                if (!isDrawing || mode !== 'draw') return;
                const pointer = canvasInstance.getPointer(options.e);
                const line = canvasInstance._currentLine;
                if (line) {
                    line.set({x2: pointer.x, y2: pointer.y});
                    console.log('Mouse Move - Updating Line:', {x1: line.x1, y1: line.y1, x2: line.x2, y2: line.y2});
                    updateLineInfo(line);
                    canvasInstance.renderAll();
                }
            });

            Swal.getPopup().addEventListener('keydown', (e) => {
                if (e.key === 'Delete' || e.key === 'Backspace') {
                    if (mode !== 'select') return;
                    const active = canvasInstance.getActiveObject();
                    if (active) {
                        removeLineInfo(active);
                        canvasInstance.remove(active);
                        canvasInstance.discardActiveObject();
                        canvasInstance.renderAll();
                    }
                }
            });

            document.getElementById('colorPicker').addEventListener('input', (e) => {
                if (mode !== 'select') return;
                const activeObject = canvasInstance.getActiveObject();
                if (activeObject && activeObject.set) {
                    activeObject.set({stroke: e.target.value});
                    if (activeObject.type === 'line') {
                        updateLineInfo(activeObject);
                    }
                    canvasInstance.renderAll();
                }
            });

            document.getElementById('drawModeBtn').click();
        },
        preConfirm: async () => {
            if (!canvasInstance) {
                Swal.showValidationMessage("Không thể truy cập canvas. Vui lòng thử lại.");
                return false;
            }

            const lines = canvasInstance.getObjects('line');
            if (!lines || lines.length === 0) {
                Swal.showValidationMessage('Chưa vẽ gì cả! Vui lòng vẽ ít nhất một đối tượng trước khi gửi.');
                return false;
            }

            const normalizedData = {
                objects: lines.map(line => {
                    return {
                        type: 'line',
                        x1: line.x1,
                        y1: line.y1,
                        x2: line.x2,
                        y2: line.y2,
                        stroke: line.stroke,
                        strokeWidth: line.strokeWidth,
                        id: line.id || generateUUID(),
                        label: line.label || 'Unnamed',
                        classes: line.classes || [],
                        direction: line.direction || 'Unknown'
                    };
                })
            };

            console.log('Normalized Data before sending:', JSON.stringify(normalizedData, null, 2));

            const confirmSubmit = await Swal.fire({
                title: 'Xác nhận gửi dữ liệu',
                text: `Bạn có muốn gửi ${normalizedData.objects.length} đối tượng?`,
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Gửi',
                cancelButtonText: 'Hủy',
                customClass: {
                    confirmButton: 'btn btn-primary',
                    cancelButton: 'btn btn-danger'
                },
                buttonsStyling: false
            });

            if (!confirmSubmit.isConfirmed) {
                return false;
            }

            Swal.fire({
                title: 'Đang gửi dữ liệu...',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    },
                    body: JSON.stringify({
                        '_action': 'updateLines',
                        'lines': normalizedData.objects
                    }),
                });

                const data = await response.json();
                console.log('Server Response:', data);

                if (data.success) {
                    if (data.lines) {
                        initialCanvasData = data.lines;
                    }
                    await Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: 'Dữ liệu đã được lưu vào cơ sở dữ liệu!',
                    });
                    return true;
                } else {
                    await Swal.fire({
                        icon: 'error',
                        title: 'Lỗi',
                        text: data.message || 'Có lỗi xảy ra khi lưu dữ liệu.',
                    });
                    return false;
                }
            } catch (error) {
                console.error('Error:', error);
                await Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Có lỗi xảy ra khi gửi dữ liệu. Vui lòng thử lại.',
                });
                return false;
            }
        },
        willClose: () => {
            if (cameraStream) {
                const tracks = cameraStream.getTracks();
                tracks.forEach(track => {
                    track.stop();
                    console.log('Track stopped:', track);
                });
                cameraStream = null;
            }
            const video = document.getElementById('videoStream');
            if (video) {
                video.srcObject = null;
            }
            if (canvasInstance) {
                canvasInstance.dispose();
            }
        }
    });
}
