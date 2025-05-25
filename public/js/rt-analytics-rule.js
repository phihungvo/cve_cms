
function showUiDraw() {
    let cameraStream = null;
    let canvasInstance = null;

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
        <div style="position: relative; width: 75%; height: 100%;">
            <video id="videoStream" width="100%" height="100%" autoplay muted playsinline
                style="position: absolute; top: 0; left: 0; z-index: 1;">
                Your browser does not support the video tag.
            </video>
            <canvas id="drawingCanvas" width="600" height="450"
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

            let mode = 'draw';
            let isDrawing = false;

            // Hàm xử lý và nạp dữ liệu vào canvas
            function loadCanvasData() {
                let processedCanvasData = null;
                const canvasOffsetX = canvasInstance.width / 2; // Offset dựa trên kích thước canvas hiện tại
                const canvasOffsetY = canvasInstance.height / 2;

                if (initialCanvasData && Array.isArray(initialCanvasData)) {
                    const normalizedObjects = initialCanvasData.map(obj => {
                        if (obj.type === 'line') {
                            return {
                                type: 'line',
                                x1: obj.x1 + canvasOffsetX,
                                y1: obj.y1 + canvasOffsetY,
                                x2: obj.x2 + canvasOffsetX,
                                y2: obj.y2 + canvasOffsetY,
                                stroke: obj.stroke || '#000000',
                                strokeWidth: obj.strokeWidth || 4,
                                groupId: obj.groupId || Date.now() + Math.random()
                            };
                        } else if (obj.type === 'text') {
                            return {
                                type: 'text',
                                text: obj.text,
                                left: obj.left + (obj.originX === 'center' ? 0 : canvasOffsetX),
                                top: obj.top + canvasOffsetY,
                                fill: obj.fill || '#000000',
                                fontSize: obj.fontSize || 12,
                                originX: obj.originX || 'center',
                                originY: obj.originY || 'center',
                                groupId: obj.groupId || Date.now() + Math.random()
                            };
                        }
                        return obj;
                    });

                    // Gán groupId cho cặp line và text dựa trên vị trí gần nhau
                    for (let i = 0; i < normalizedObjects.length - 1; i++) {
                        if (normalizedObjects[i].type === 'line' && normalizedObjects[i + 1].type === 'text') {
                            const lineMidX = (normalizedObjects[i].x1 + normalizedObjects[i].x2) / 2;
                            const lineMidY = (normalizedObjects[i].y1 + normalizedObjects[i].y2) / 2;
                            const textX = normalizedObjects[i + 1].left;
                            const textY = normalizedObjects[i + 1].top;
                            if (Math.abs(lineMidX - textX) < 50 && Math.abs(lineMidY - textY) < 50) {
                                const groupId = Date.now() + Math.random();
                                normalizedObjects[i].groupId = groupId;
                                normalizedObjects[i + 1].groupId = groupId;
                            }
                        }
                    }

                    processedCanvasData = {
                        version: '5.3.0',
                        objects: normalizedObjects
                    };
                } else {
                    processedCanvasData = {
                        version: '5.3.0',
                        objects: []
                    };
                }

                // Nạp dữ liệu vào canvas
                if (processedCanvasData.objects.length > 0) {
                    try {
                        console.log('Processed Canvas Data:', JSON.stringify(processedCanvasData, null, 2));
                        canvasInstance.loadFromJSON(processedCanvasData, () => {
                            const lines = {};
                            canvasInstance.forEachObject(obj => {
                                console.log('Object loaded:', obj);
                                if (obj.type !== 'textbox') {
                                    obj.selectable = false;
                                }
                                if (obj.groupId) {
                                    if (obj.type === 'line') {
                                        lines[obj.groupId] = {line: obj, text: null};
                                    } else if (obj.type === 'text') {
                                        if (lines[obj.groupId]) {
                                            lines[obj.groupId].text = obj;
                                        } else {
                                            lines[obj.groupId] = {line: null, text: obj};
                                        }
                                    }
                                }
                            });

                            console.log('Grouped lines:', lines);

                            Object.values(lines).forEach(group => {
                                if (group.line && group.text) {
                                    group.line._associatedText = group.text;
                                    addLineInfo(group.line, group.text.text);
                                } else if (group.line) {
                                    const midX = (group.line.x1 + group.line.x2) / 2;
                                    const midY = (group.line.y1 + group.line.y2) / 2;
                                    const textBox = new fabric.Textbox('Enter Name', {
                                        left: midX - 50,
                                        top: midY - 20,
                                        width: 100,
                                        fontSize: 12,
                                        backgroundColor: '#fff',
                                        borderColor: '#000',
                                        fill: '#000',
                                        editable: true,
                                        selectable: true,
                                        groupId: group.line.groupId
                                    });
                                    canvasInstance.add(textBox);
                                    group.line._associatedText = textBox;
                                    canvasInstance.setActiveObject(textBox);
                                    textBox.enterEditing();
                                    textBox.selectAll();

                                    textBox.on('editing:exited', () => {
                                        const lineName = textBox.text.trim();
                                        if (lineName && lineName !== 'Enter Name') {
                                            const text = new fabric.Text(lineName, {
                                                left: midX,
                                                top: midY - 20,
                                                fontSize: 12,
                                                fill: group.line.stroke,
                                                selectable: false,
                                                originX: 'center',
                                                originY: 'center',
                                                groupId: group.line.groupId
                                            });
                                            canvasInstance.add(text);
                                            group.line._associatedText = text;
                                            addLineInfo(group.line, lineName);
                                        }
                                        canvasInstance.remove(textBox);
                                        canvasInstance.renderAll();
                                    });
                                }
                            });

                            canvasInstance.renderAll();
                        }, (error) => {
                            console.error('Error loading canvas data:', error);
                        });
                    } catch (error) {
                        console.error('Exception during loadFromJSON:', error);
                    }
                }
            }

            // Truy cập camera và đồng bộ kích thước canvas
            if (navigator.mediaDevices && navigator.mediaDevices.getUserMedia) {
                navigator.mediaDevices.getUserMedia({
                    video: true,
                    audio: false
                })
                    .then(stream => {
                        cameraStream = stream;
                        video.srcObject = stream;
                        video.play().catch(err => console.error('Video play error:', err));

                        // Đồng bộ kích thước canvas với video khi video được tải
                        video.addEventListener('loadedmetadata', () => {
                            console.log('Video loadedmetadata - Width:', video.videoWidth, 'Height:', video.videoHeight);
                            // Cập nhật kích thước canvas bằng Fabric.js
                            canvasInstance.setDimensions({
                                width: video.videoWidth || 600, // Giá trị mặc định nếu video không có kích thước
                                height: video.videoHeight || 450
                            }, {backstoreOnly: true}); // Chỉ cập nhật backend, tránh vẽ lại ngay

                            // Vẽ lại các object sau khi kích thước được đồng bộ
                            loadCanvasData();
                            canvasInstance.renderAll(); // Đảm bảo vẽ lại canvas
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

            function addLineInfo(line, name = 'Unnamed') {
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
                            <input value="${name}"/>
                        </div>
                        <div>
                            <strong>Color:</strong>
                            <input type="color" value="${line.stroke || '#000000'}">
                        </div>
                        <div><strong>Length:</strong> ${length}px</div>
                    `;
                lineInfoList.appendChild(lineInfo);
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
                const groupId = Date.now() + Math.random();
                const line = new fabric.Line([pointer.x, pointer.y, pointer.x, pointer.y], {
                    stroke: document.getElementById('colorPicker').value,
                    strokeWidth: 4,
                    selectable: true,
                    groupId: groupId
                });
                canvasInstance.add(line);
                canvasInstance._currentLine = line;
            });

            canvasInstance.on('mouse:up', (options) => {
                if (mode !== 'draw' || !isDrawing) return;
                isDrawing = false;

                const line = canvasInstance._currentLine;
                canvasInstance._currentLine = null;

                if (!line) return;

                const isLineTooShort = line.x1 === line.x2 && line.y1 === line.y2;
                if (isLineTooShort) {
                    canvasInstance.remove(line);
                    return;
                }

                const existingLines = canvasInstance.getObjects('line').length;
                const defaultLineName = `Line ${existingLines}`;

                const midX = (line.x1 + line.x2) / 2;
                const midY = (line.y1 + line.y2) / 2;
                const textBoxWidth = 100;
                const offsetY = -20;

                const textBox = new fabric.Textbox(defaultLineName, {
                    left: midX - textBoxWidth / 2,
                    top: midY + offsetY,
                    width: textBoxWidth,
                    fontSize: 12,
                    backgroundColor: '#fff',
                    borderColor: '#000',
                    fill: '#000',
                    editable: true,
                    selectable: true,
                    groupId: line.groupId
                });
                canvasInstance.add(textBox);
                canvasInstance.setActiveObject(textBox);
                textBox.enterEditing();
                textBox.selectAll();

                textBox.on('editing:exited', () => {
                    const lineName = textBox.text.trim();
                    if (lineName && lineName !== 'Enter Name') {
                        line.set({name: lineName});
                        const text = new fabric.Text(lineName, {
                            left: midX,
                            top: midY + offsetY,
                            fontSize: 12,
                            fill: line.stroke,
                            selectable: false,
                            originX: 'center',
                            originY: 'center',
                            groupId: line.groupId
                        });
                        canvasInstance.add(text);
                        line._associatedText = text;
                        addLineInfo(line, lineName);
                    }
                    canvasInstance.remove(textBox);
                    canvasInstance.renderAll();
                });
            });

            canvasInstance.on('mouse:move', (options) => {
                if (!isDrawing || mode !== 'draw') return;
                const pointer = canvasInstance.getPointer(options.e);
                const line = canvasInstance._currentLine;
                if (line) {
                    line.set({x2: pointer.x, y2: pointer.y});
                    canvasInstance.renderAll();
                }
            });

            Swal.getPopup().addEventListener('keydown', (e) => {
                if (e.key === 'Delete' || e.key === 'Backspace') {
                    if (mode !== 'select') return;
                    const active = canvasInstance.getActiveObject();
                    if (active) {
                        if (active.type === 'line') {
                            removeLineInfo(active);
                            const associatedText = canvasInstance.getObjects().find(obj =>
                                obj.type === 'text' && obj.groupId === active.groupId
                            );
                            if (associatedText) {
                                canvasInstance.remove(associatedText);
                            }
                        }
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
                        const associatedText = canvasInstance.getObjects().find(obj =>
                            obj.type === 'text' && obj.groupId === activeObject.groupId
                        );
                        if (associatedText) {
                            associatedText.set({fill: e.target.value});
                            const lineInfo = document.getElementById('lineInfoList')
                                .querySelector(`li[data-line-id="${activeObject.id}"]`);
                            if (lineInfo) {
                                const name = associatedText.text || 'Unnamed';
                                lineInfo.innerHTML = `
                                        <div>
                                            <label>Name</label>
                                            <input value="${name}"/>
                                        </div>
                                        <div>
                                            <strong>Color:</strong>
                                            <input type="color" value="${activeObject.stroke}">
                                        </div>
                                        <div><strong>Length:</strong> ${Math.sqrt(
                                    Math.pow(activeObject.x2 - activeObject.x1, 2) +
                                    Math.pow(activeObject.y2 - activeObject.y1, 2)
                                ).toFixed(2)}px</div>
                                    `;
                            }
                        }
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

            const canvasData = canvasInstance.toJSON();
            console.log('Canvas Data before sending:', JSON.stringify(canvasData, null, 2));

            if (!canvasData.objects || canvasData.objects.length === 0) {
                Swal.showValidationMessage('Chưa vẽ gì cả! Vui lòng vẽ ít nhất một đối tượng trước khi gửi.');
                return false;
            }

            // Chuẩn hóa dữ liệu trước khi gửi
            const normalizedData = {
                objects: canvasData.objects.map(obj => {
                    if (obj.type === 'line') {
                        return {
                            type: 'line',
                            x1: obj.x1,
                            y1: obj.y1,
                            x2: obj.x2,
                            y2: obj.y2,
                            stroke: obj.stroke,
                            strokeWidth: obj.strokeWidth,
                            groupId: obj.groupId
                        };
                    } else if (obj.type === 'text') {
                        return {
                            type: 'text',
                            text: obj.text,
                            left: obj.left,
                            top: obj.top,
                            fill: obj.fill,
                            fontSize: obj.fontSize,
                            originX: obj.originX,
                            originY: obj.originY,
                            groupId: obj.groupId
                        };
                    }
                    return obj;
                })
            };

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
