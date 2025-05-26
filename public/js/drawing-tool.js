/**
 * Drawing Tool Module
 * Handles canvas drawing functionality for analytics rules
 */
let DrawingTool = (function () {
    // Private variables
    let canvas, ctx, videoElement;
    let isDrawing = false;
    let isResizing = false;
    let resizeHandle = null;
    const handleSize = 8;
    let currentMode = 'line'; // Default drawing mode
    let editMode = false;
    let selectedShape = null;
    let startX, startY, currentX, currentY;
    let points = []; // Points for polygon
    let shapes = []; // All drawn shapes
    let lines = []; // Store lines separately
    let zones = []; // Store zones separately
    let redoShapes = []; // Shapes that were undone
    let instanceUuid = null; // UUID instance
    let currentColor = [255, 0, 0]; // Default color (RGB)
    let currentLabel = '';
    let currentRuleType = 'Line Crossing'; // Rule type từ bên ngoài
    let detectObjects = ['Person']; // Detect objects từ bên ngoài
    let ruleName = ''; // Rule name từ bên ngoài
    let direction = 'both'; // Default direction
    const defaultWidth = 3;
    let shapeIdCounter = 0; // Đếm để tạo ID duy nhất cho shape

    // Hàm tạo ID duy nhất cho shape
    const generateShapeId = () => {
        return `shape_${instanceUuid}_${shapeIdCounter++}`;
    };

    // Generate random color in RGB
    const generateRandomColor = () => {
        const colors = [
            [255, 0, 0], [0, 255, 0], [0, 0, 255], [255, 255, 0], [255, 0, 255], [0, 255, 255],
            [255, 128, 0], [128, 0, 255], [0, 128, 255], [128, 255, 0], [255, 0, 128], [0, 255, 128]
        ];

        const usedColors = shapes.map(shape => JSON.stringify(shape.color));
        const availableColors = colors.filter(color => !usedColors.includes(JSON.stringify(color)));

        if (availableColors.length === 0) {
            return [
                Math.floor(Math.random() * 256),
                Math.floor(Math.random() * 256),
                Math.floor(Math.random() * 256)
            ];
        }

        return availableColors[Math.floor(Math.random() * availableColors.length)];
    };

    // Convert RGB array to hex for canvas rendering
    const rgbToHex = (rgb) => {
        if (!Array.isArray(rgb) || rgb.length !== 3 || rgb.some(x => typeof x !== 'number')) {
            console.error('Invalid RGB input:', rgb);
            return '#000000';
        }
        return '#' + rgb.map(x => {
            const hex = x.toString(16);
            return hex.length === 1 ? '0' + hex : hex;
        }).join('');
    };

    // Check if point is near a shape (for selection)
    const isPointNearShape = (x, y, shape, tolerance = 15) => {
        if (shape.type === 'line') {
            const dx = shape.endX - shape.startX;
            const dy = shape.endY - shape.startY;
            const length = Math.sqrt(dx * dx + dy * dy);

            if (length === 0) return Math.sqrt((x - shape.startX) ** 2 + (y - shape.startY) ** 2) <= tolerance;

            const t = Math.max(0, Math.min(1, ((x - shape.startX) * dx + (y - shape.startY) * dy) / (length * length)));
            const projection = {
                x: shape.startX + t * dx,
                y: shape.startY + t * dy
            };

            const distance = Math.sqrt((x - projection.x) ** 2 + (y - projection.y) ** 2);
            return distance <= tolerance;
        } else if (shape.type === 'rect') {
            // Kiểm tra các điểm điều khiển (resize handles)
            const handles = [
                { x: shape.startX, y: shape.startY, handle: 'top-left' },
                { x: shape.startX + shape.width, y: shape.startY, handle: 'top-right' },
                { x: shape.startX, y: shape.startY + shape.height, handle: 'bottom-left' },
                { x: shape.startX + shape.width, y: shape.startY + shape.height, handle: 'bottom-right' }
            ];

            for (const h of handles) {
                if (Math.abs(x - h.x) <= handleSize && Math.abs(y - h.y) <= handleSize) {
                    return { isHandle: true, handle: h.handle };
                }
            }

            // Kiểm tra bên trong hình chữ nhật để di chuyển
            return {
                isHandle: false,
                inside: x >= shape.startX && x <= shape.startX + shape.width &&
                    y >= shape.startY && y <= shape.startY + shape.height
            };
        } else if (shape.type === 'poly') {
            let inside = false;
            for (let i = 0, j = shape.points.length - 1; i < shape.points.length; j = i++) {
                if (((shape.points[i].y > y) !== (shape.points[j].y > y)) &&
                    (x < (shape.points[j].x - shape.points[i].x) * (y - shape.points[i].y) / (shape.points[j].y - shape.points[i].y) + shape.points[i].x)) {
                    inside = !inside;
                }
            }
            return { isHandle: false, inside };
        }
        return { isHandle: false, inside: false };
    };

    // Find shape at given coordinates
    const findShapeAtPoint = (x, y) => {
        for (let i = shapes.length - 1; i >= 0; i--) {
            const pointInfo = isPointNearShape(x, y, shapes[i]);
            if (pointInfo.isHandle || pointInfo.inside) {
                return shapes[i];
            }
        }
        return null;
    };

    // Initialize DOM elements
    const initElements = function () {
        canvas = document.getElementById('canvasOverlay');
        ctx = canvas.getContext('2d');
        videoElement = document.getElementById('videoElement');

        if (!canvas || !ctx || !videoElement) {
            console.error('Required elements not found');
            return false;
        }
        return true;
    };

    // Update available drawing modes based on rule type
    const updateDrawingModes = () => {
        const lineBtn = document.getElementById('lineBtn');
        const rectBtn = document.getElementById('rectBtn');
        const polyBtn = document.getElementById('polyBtn');
        const closePolyBtn = document.getElementById('closePolyBtn');

        if (currentRuleType === 'Line Crossing') {
            lineBtn.style.display = 'inline-block';
            rectBtn.style.display = 'none';
            polyBtn.style.display = 'none';
            closePolyBtn.style.display = 'none';
            setActiveMode('line');
        } else {
            lineBtn.style.display = 'none';
            rectBtn.style.display = 'inline-block';
            polyBtn.style.display = 'inline-block';
            closePolyBtn.style.display = currentMode === 'poly' ? 'inline-block' : 'none';
            if (currentMode === 'line') {
                setActiveMode('rect');
            }
        }

        // Cập nhật danh sách shapes hiển thị
        shapes = currentRuleType === 'Line Crossing' ? [...lines] : [...zones];
        redrawAll();
        updateShapesList();
    };

    // Initialize event listeners
    const initEventListeners = function () {
        const lineBtn = document.getElementById('lineBtn');
        const rectBtn = document.getElementById('rectBtn');
        const polyBtn = document.getElementById('polyBtn');
        const closePolyBtn = document.getElementById('closePolyBtn');
        const undoBtn = document.getElementById('undoBtn');
        const redoBtn = document.getElementById('redoBtn');
        const clearBtn = document.getElementById('clearBtn');
        const colorPicker = document.getElementById('colorPicker');
        const labelInput = document.getElementById('labelInput');
        const directionSelect = document.getElementById('directionSelect');

        if (!undoBtn || !redoBtn || !clearBtn || !colorPicker || !labelInput || !directionSelect) {
            console.error('One or more UI elements not found');
            return;
        }

        // Drawing tool buttons
        if (lineBtn) lineBtn.addEventListener('click', () => setActiveMode('line'));
        if (rectBtn) rectBtn.addEventListener('click', () => setActiveMode('rect'));
        if (polyBtn) {
            polyBtn.addEventListener('click', () => {
                setActiveMode('poly');
                points = [];
                updateClosePolyButton();
            });
        }
        if (closePolyBtn) {
            closePolyBtn.addEventListener('click', () => {
                if (points.length >= 2) {
                    points.push({ ...points[0] });
                    finishPolygon();
                }
            });
        }

        // Undo, redo, clear
        undoBtn.addEventListener('click', () => {
            if (shapes.length > 0) {
                redoShapes.push(shapes.pop());
                // Cập nhật lines hoặc zones tương ứng
                if (currentRuleType === 'Line Crossing') {
                    lines = [...shapes];
                } else {
                    zones = [...shapes];
                }
                redrawAll();
                updateShapesList();
            }
        });
        redoBtn.addEventListener('click', () => {
            if (redoShapes.length > 0) {
                shapes.push(redoShapes.pop());
                // Cập nhật lines hoặc zones tương ứng
                if (currentRuleType === 'Line Crossing') {
                    lines = [...shapes];
                } else {
                    zones = [...shapes];
                }
                redrawAll();
                updateShapesList();
            }
        });
        clearBtn.addEventListener('click', () => {
            shapes = [];
            redoShapes = [];
            points = [];
            // Xóa lines hoặc zones tương ứng
            if (currentRuleType === 'Line Crossing') {
                lines = [];
            } else {
                zones = [];
            }
            redrawAll();
            updateShapesList();
            updateClosePolyButton();
        });

        // Color, label, and direction inputs
        colorPicker.addEventListener('change', () => {
            const hex = colorPicker.value;
            currentColor = [
                parseInt(hex.substr(1, 2), 16),
                parseInt(hex.substr(3, 2), 16),
                parseInt(hex.substr(5, 2), 16)
            ];
            if (selectedShape) {
                selectedShape.color = [...currentColor];
                redrawAll();
                updateShapesList();
            }
        });
        labelInput.addEventListener('input', () => {
            currentLabel = labelInput.value;
            if (selectedShape) {
                selectedShape.label = currentLabel;
                redrawAll();
                updateShapesList();
            }
        });
        directionSelect.addEventListener('change', () => {
            direction = directionSelect.value;
            if (selectedShape) {
                selectedShape.direction = direction;
                redrawAll();
                updateShapesList();
            }
        });

        // Canvas drawing events
        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', endDrawing);
        canvas.addEventListener('dblclick', finishPolygon);

        // Touch events for mobile
        canvas.addEventListener('touchstart', handleTouchStart);
        canvas.addEventListener('touchmove', handleTouchMove);
        canvas.addEventListener('touchend', handleTouchEnd);

        // Video and canvas resizing
        videoElement.addEventListener('loadeddata', resizeCanvas);
        window.addEventListener('resize', resizeCanvas);
        videoElement.onloadedmetadata = () => {
            videoElement.play();
            resizeCanvas();
        };
    };

    // Touch event handlers
    const handleTouchStart = (e) => {
        e.preventDefault();
        if (e.touches.length === 1) {
            const touch = e.touches[0];
            canvas.dispatchEvent(new MouseEvent('mousedown', {
                clientX: touch.clientX,
                clientY: touch.clientY
            }));
        }
    };

    const handleTouchMove = (e) => {
        e.preventDefault();
        if (e.touches.length === 1) {
            const touch = e.touches[0];
            canvas.dispatchEvent(new MouseEvent('mousemove', {
                clientX: touch.clientX,
                clientY: touch.clientY
            }));
        }
    };

    const handleTouchEnd = (e) => {
        e.preventDefault();
        canvas.dispatchEvent(new MouseEvent('mouseup', {}));
    };

    // Set active drawing mode
    const setActiveMode = function (mode) {
        if ((currentRuleType === 'Line Crossing' && mode !== 'line') ||
            (currentRuleType !== 'Line Crossing' && mode === 'line')) {
            return;
        }

        currentMode = mode;
        editMode = mode === 'edit';
        selectedShape = null;

        const buttons = [
            document.getElementById('lineBtn'),
            document.getElementById('rectBtn'),
            document.getElementById('polyBtn')
        ].filter(Boolean);

        buttons.forEach(btn => btn.classList.remove('active'));
        if (mode === 'line' && buttons[0]) buttons[0].classList.add('active');
        else if (mode === 'rect' && buttons[1]) buttons[1].classList.add('active');
        else if (mode === 'poly' && buttons[2]) buttons[2].classList.add('active');

        const closePolyBtn = document.getElementById('closePolyBtn');
        if (closePolyBtn) {
            closePolyBtn.style.display = mode === 'poly' ? 'inline-block' : 'none';
            closePolyBtn.disabled = mode !== 'poly' || points.length < 2;
        }

        points = [];
        redrawAll();

        canvas.style.cursor = editMode ? 'default' : 'crosshair';
    };

    // Update close polygon button state
    const updateClosePolyButton = () => {
        const closePolyBtn = document.getElementById('closePolyBtn');
        if (closePolyBtn) {
            closePolyBtn.disabled = currentMode !== 'poly' || points.length < 2;
        }
    };

    // Resize canvas to match video
    const resizeCanvas = () => {
        const wrapper = document.getElementById('videoWrapper');
        canvas.width = wrapper.offsetWidth;
        canvas.height = wrapper.offsetHeight;
        canvas.style.width = wrapper.offsetWidth + 'px';
        canvas.style.height = wrapper.offsetHeight + 'px';
        videoElement.style.width = wrapper.offsetWidth + 'px';
        videoElement.style.height = wrapper.offsetHeight + 'px';
        redrawAll();
    };

    // Start drawing
    const startDrawing = (e) => {
        const rect = canvas.getBoundingClientRect();
        startX = e.clientX - rect.left;
        startY = e.clientY - rect.top;
        currentX = startX;
        currentY = startY;

        if (editMode) {
            const clickedShape = findShapeAtPoint(startX, startY);
            if (clickedShape) {
                selectedShape = clickedShape;
                document.getElementById('colorPicker').value = rgbToHex(clickedShape.color);
                document.getElementById('labelInput').value = clickedShape.label;
                currentColor = [...clickedShape.color];
                currentLabel = clickedShape.label;
                direction = clickedShape.direction || 'both';
                document.getElementById('directionSelect').value = direction;

                isDrawing = true;

                const pointInfo = isPointNearShape(startX, startY, clickedShape);
                if (clickedShape.type === 'rect' && pointInfo.isHandle) {
                    isResizing = true;
                    resizeHandle = pointInfo.handle;
                } else if (pointInfo.inside || pointInfo === true) {
                    isResizing = false;
                    if (clickedShape.type === 'line') {
                        selectedShape.offsetX = startX - clickedShape.startX;
                        selectedShape.offsetY = startY - clickedShape.startY;
                    } else if (clickedShape.type === 'rect') {
                        selectedShape.offsetX = startX - clickedShape.startX;
                        selectedShape.offsetY = startY - clickedShape.startY;
                    } else if (clickedShape.type === 'poly') {
                        selectedShape.offsetX = startX;
                        selectedShape.offsetY = startY;
                    }
                }

                redrawAll();
                updateShapesList();
            } else {
                selectedShape = null;
                redrawAll();
                updateShapesList();
            }
            return;
        }

        isDrawing = true;

        if (currentMode === 'poly') {
            points.push({ x: startX, y: startY, color: rgbToHex(currentColor) });
            ctx.beginPath();
            ctx.arc(startX, startY, 3, 0, Math.PI * 2);
            ctx.fillStyle = rgbToHex(currentColor);
            ctx.fill();
            if (points.length > 1) {
                ctx.beginPath();
                ctx.moveTo(points[points.length - 2].x, points[points.length - 2].y);
                ctx.lineTo(startX, startY);
                ctx.strokeStyle = rgbToHex(currentColor);
                ctx.lineWidth = defaultWidth;
                ctx.stroke();
            }
            updateClosePolyButton();
        }
    };

    // Draw while moving
    const draw = (e) => {
        if (!isDrawing) return;

        const rect = canvas.getBoundingClientRect();
        currentX = e.clientX - rect.left;
        currentY = e.clientY - rect.top;

        if (editMode && selectedShape) {
            const deltaX = currentX - startX;
            const deltaY = currentY - startY;

            if (isResizing && selectedShape.type === 'rect') {
                // Thay đổi kích thước hình chữ nhật
                if (resizeHandle === 'top-left') {
                    selectedShape.width += selectedShape.startX - currentX;
                    selectedShape.height += selectedShape.startY - currentY;
                    selectedShape.startX = currentX;
                    selectedShape.startY = currentY;
                } else if (resizeHandle === 'top-right') {
                    selectedShape.width = currentX - selectedShape.startX;
                    selectedShape.height += selectedShape.startY - currentY;
                    selectedShape.startY = currentY;
                } else if (resizeHandle === 'bottom-left') {
                    selectedShape.width += selectedShape.startX - currentX;
                    selectedShape.startX = currentX;
                    selectedShape.height = currentY - selectedShape.startY;
                } else if (resizeHandle === 'bottom-right') {
                    selectedShape.width = currentX - selectedShape.startX;
                    selectedShape.height = currentY - selectedShape.startY;
                }
            } else {
                // Di chuyển shape
                if (selectedShape.type === 'line') {
                    selectedShape.startX += deltaX;
                    selectedShape.startY += deltaY;
                    selectedShape.endX += deltaX;
                    selectedShape.endY += deltaY;
                } else if (selectedShape.type === 'rect') {
                    selectedShape.startX += deltaX;
                    selectedShape.startY += deltaY;
                } else if (selectedShape.type === 'poly') {
                    selectedShape.points.forEach(point => {
                        point.x += deltaX;
                        point.y += deltaY;
                    });
                }
            }

            // Cập nhật lines hoặc zones
            if (currentRuleType === 'Line Crossing') {
                lines = [...shapes];
            } else {
                zones = [...shapes];
            }

            startX = currentX;
            startY = currentY;
            redrawAll();
            return;
        }

        if (currentMode === 'line' || currentMode === 'rect') {
            redrawAll();
            ctx.beginPath();
            ctx.strokeStyle = rgbToHex(currentColor);
            ctx.lineWidth = defaultWidth;
            if (currentMode === 'line') {
                ctx.moveTo(startX, startY);
                ctx.lineTo(currentX, currentY);
            } else if (currentMode === 'rect') {
                ctx.rect(startX, startY, currentX - startX, currentY - startY);
            }
            ctx.stroke();
        }
    };

    // End drawing
    const endDrawing = (e) => {
        if (!isDrawing) return;
        isDrawing = false;
        isResizing = false;
        resizeHandle = null;

        if (editMode && selectedShape) {
            selectedShape.color = [...currentColor];
            selectedShape.label = currentLabel;
            selectedShape.direction = direction;
            // Cập nhật lines hoặc zones
            if (currentRuleType === 'Line Crossing') {
                lines = [...shapes];
            } else {
                zones = [...shapes];
            }
            updateShapesList();
            redrawAll();
            return;
        }

        const rect = canvas.getBoundingClientRect();
        const endX = e.clientX - rect.left;
        const endY = e.clientY - rect.top;

        if (currentMode === 'line') {
            const newShape = {
                id: generateShapeId(),
                type: 'line',
                startX, startY, endX, endY,
                color: [...currentColor],
                label: currentLabel || `Đường thẳng`,
                detect_objects: [...detectObjects],
                direction: direction,
                rule_name: ruleName,
                instance_uuid: instanceUuid
            };
            shapes.push(newShape);
            lines.push(newShape);
            redoShapes = [];
        } else if (currentMode === 'rect') {
            const newShape = {
                id: generateShapeId(),
                type: 'rect',
                startX, startY,
                width: endX - startX,
                height: endY - startY,
                color: [...currentColor],
                label: currentLabel || `Hình chữ nhật`,
                detect_objects: [...detectObjects],
                direction: direction,
                rule_name: ruleName,
                instance_uuid: instanceUuid
            };
            shapes.push(newShape);
            zones.push(newShape);
            redoShapes = [];
        }

        currentColor = generateRandomColor();
        document.getElementById('colorPicker').value = rgbToHex(currentColor);

        updateShapesList();
        redrawAll();
    };

    // Finish polygon
    const finishPolygon = () => {
        if (currentMode !== 'poly' || points.length < 3) return;

        const newShape = {
            id: generateShapeId(),
            type: 'poly',
            points: [...points],
            color: [...currentColor],
            label: currentLabel || `Đa giác`,
            detect_objects: [...detectObjects],
            direction: direction,
            rule_name: ruleName,
            instance_uuid: instanceUuid
        };
        shapes.push(newShape);
        zones.push(newShape);
        points = [];

        currentColor = generateRandomColor();
        document.getElementById('colorPicker').value = rgbToHex(currentColor);

        updateClosePolyButton();
        updateShapesList();
        redrawAll();
    };

    // Edit shape label inline
    const editShapeLabel = (shape, listItem) => {
        const labelSpan = listItem.querySelector('.label-span');
        const currentText = labelSpan.textContent;
        const input = document.createElement('input');
        input.type = 'text';
        input.value = currentText;
        input.className = 'inline-edit-input px-1 py-0 border rounded text-sm';
        input.style.width = '150px';

        labelSpan.style.display = 'none';
        labelSpan.parentNode.insertBefore(input, labelSpan);
        input.focus();
        input.select();

        const finishEdit = () => {
            const newLabel = input.value.trim();
            if (newLabel && newLabel !== currentText) {
                shape.label = newLabel;
                labelSpan.textContent = newLabel;
                // Cập nhật lines hoặc zones
                if (currentRuleType === 'Line Crossing') {
                    const index = lines.findIndex(s => s.id === shape.id);
                    if (index !== -1) lines[index].label = newLabel;
                } else {
                    const index = zones.findIndex(s => s.id === shape.id);
                    if (index !== -1) zones[index].label = newLabel;
                }
            }
            input.remove();
            labelSpan.style.display = 'inline';
            updateShapesList();
            redrawAll();
        };

        input.addEventListener('blur', finishEdit);
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                finishEdit();
            } else if (e.key === 'Escape') {
                input.remove();
                labelSpan.style.display = 'inline';
            }
        });
    };

    // Update shapes list
    const updateShapesList = () => {
        const shapesList = document.getElementById('shapesList');
        shapesList.innerHTML = '';

        const filteredShapes = shapes;

        filteredShapes.forEach(shape => {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between py-1 px-2 rounded hover:bg-gray-100 mb-1';

            if (selectedShape && selectedShape.id === shape.id) {
                li.classList.add('bg-blue-100', 'border', 'border-blue-300');
            }

            const infoContainer = document.createElement('div');
            infoContainer.className = 'flex items-center flex-1';

            const colorIndicator = document.createElement('div');
            colorIndicator.className = 'w-4 h-4 rounded border border-gray-300 mr-2';
            colorIndicator.style.backgroundColor = rgbToHex(shape.color);

            const labelSpan = document.createElement('span');
            labelSpan.className = 'label-span cursor-pointer hover:underline flex-1';
            labelSpan.textContent = shape.label || `Shape ${shape.id}`;
            labelSpan.style.color = rgbToHex(shape.color);

            labelSpan.addEventListener('click', (e) => {
                e.stopPropagation();
                editShapeLabel(shape, li);
            });

            infoContainer.appendChild(colorIndicator);
            infoContainer.appendChild(labelSpan);

            const buttonsContainer = document.createElement('div');
            buttonsContainer.className = 'flex gap-1';

            const selectBtn = document.createElement('button');
            selectBtn.textContent = '✏️';
            selectBtn.className = 'px-2 py-1 text-sm text-blue-600 hover:text-blue-800 hover:bg-blue-50 rounded';
            selectBtn.title = 'Chọn để chỉnh sửa';
            selectBtn.onclick = (e) => {
                e.stopPropagation();
                selectedShape = shape;
                document.getElementById('colorPicker').value = rgbToHex(shape.color);
                document.getElementById('labelInput').value = shape.label;
                currentColor = [...shape.color];
                currentLabel = shape.label;
                direction = shape.direction || 'both';
                document.getElementById('directionSelect').value = direction;
                setActiveMode('edit');
                redrawAll();
                updateShapesList();
            };

            const deleteBtn = document.createElement('button');
            deleteBtn.textContent = '✕';
            deleteBtn.className = 'px-2 py-1 text-sm text-red-600 hover:text-red-800 hover:bg-red-50 rounded';
            deleteBtn.title = 'Xóa';
            deleteBtn.onclick = (e) => {
                e.stopPropagation();
                shapes = shapes.filter(s => s.id !== shape.id);
                if (currentRuleType === 'Line Crossing') {
                    lines = lines.filter(s => s.id !== shape.id);
                } else {
                    zones = zones.filter(s => s.id !== shape.id);
                }
                if (selectedShape && selectedShape.id === shape.id) {
                    selectedShape = null;
                }
                updateShapesList();
                redrawAll();
            };

            buttonsContainer.appendChild(selectBtn);
            buttonsContainer.appendChild(deleteBtn);

            li.appendChild(infoContainer);
            li.appendChild(buttonsContainer);
            shapesList.appendChild(li);
        });

        if (filteredShapes.length === 0) {
            const emptyMsg = document.createElement('li');
            emptyMsg.textContent = 'Chưa có hình nào được vẽ';
            emptyMsg.className = 'text-gray-500 italic';
            shapesList.appendChild(emptyMsg);
        }
    };

    // Redraw all shapes
    const redrawAll = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        const filteredShapes = shapes;

        filteredShapes.forEach(shape => {
            ctx.beginPath();
            ctx.strokeStyle = rgbToHex(shape.color || [255, 0, 0]);
            ctx.lineWidth = defaultWidth;

            if (selectedShape && selectedShape.id === shape.id) {
                ctx.lineWidth = defaultWidth + 2;
                ctx.setLineDash([5, 5]);
            } else {
                ctx.setLineDash([]);
            }

            if (shape.type === 'line') {
                ctx.moveTo(shape.startX, shape.startY);
                ctx.lineTo(shape.endX, shape.endY);
            } else if (shape.type === 'rect') {
                ctx.rect(shape.startX, shape.startY, shape.width, shape.height);
            } else if (shape.type === 'poly' && shape.points.length > 0) {
                ctx.moveTo(shape.points[0].x, shape.points[0].y);
                shape.points.slice(1).forEach(point => ctx.lineTo(point.x, point.y));
                ctx.closePath();
            }
            ctx.stroke();

            // Vẽ điểm điều khiển cho hình chữ nhật được chọn
            if (selectedShape && selectedShape.id === shape.id && shape.type === 'rect') {
                const handles = [
                    { x: shape.startX, y: shape.startY },
                    { x: shape.startX + shape.width, y: shape.startY },
                    { x: shape.startX, y: shape.startY + shape.height },
                    { x: shape.startX + shape.width, y: shape.startY + shape.height }
                ];
                handles.forEach(h => {
                    ctx.beginPath();
                    ctx.fillStyle = '#ffffff';
                    ctx.strokeStyle = '#000000';
                    ctx.lineWidth = 1;
                    ctx.rect(h.x - handleSize / 2, h.y - handleSize / 2, handleSize, handleSize);
                    ctx.fill();
                    ctx.stroke();
                });
            }

            if (shape.label) {
                ctx.font = '12px Arial';
                ctx.fillStyle = rgbToHex(shape.color || [255, 0, 0]);
                ctx.setLineDash([]);

                if (shape.type === 'line') {
                    const midX = (shape.startX + shape.endX) / 2;
                    const midY = (shape.startY + shape.endY) / 2;
                    ctx.fillText(shape.label, midX + 5, midY - 5);
                } else if (shape.type === 'rect') {
                    ctx.fillText(shape.label, shape.startX + 5, shape.startY - 5);
                } else if (shape.type === 'poly' && shape.points.length > 0) {
                    const { x: centerX, y: centerY } = shape.points.reduce((acc, p) => ({
                        x: acc.x + p.x,
                        y: acc.y + p.y
                    }), { x: 0, y: 0 });
                    ctx.fillText(shape.label, centerX / shape.points.length, centerY / shape.points.length);
                }
            }
        });

        if (currentMode === 'poly' && points.length > 0) {
            points.forEach((point, index) => {
                ctx.beginPath();
                ctx.arc(point.x, point.y, 3, 0, Math.PI * 2);
                ctx.fillStyle = rgbToHex(currentColor);
                ctx.fill();
                if (index > 0) {
                    ctx.beginPath();
                    ctx.moveTo(points[index - 1].x, points[index - 1].y);
                    ctx.lineTo(point.x, point.y);
                    ctx.strokeStyle = rgbToHex(currentColor);
                    ctx.lineWidth = defaultWidth;
                    ctx.setLineDash([]);
                    ctx.stroke();
                }
            });
        }
    };

    // Load shapes from server
    const loadShapesFromServer = (instanceId) => {
        fetch(`/cvedixrt/instances/${instanceId}/shapes`, {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(response => {
                if (!response.ok) throw new Error('Không thể tải dữ liệu từ server');
                return response.json();
            })
            .then(data => {
                if (data.success && data.data) {
                    lines = [];
                    zones = [];
                    shapes = [];

                    console.log('Server data:', data.data);

                    // Tải lines
                    if (data.data.lines && Array.isArray(data.data.lines)) {
                        lines.push(...data.data.lines.map(line => ({
                            id: generateShapeId(),
                            type: 'line',
                            startX: parseFloat(line.coordinates.startX) || 0,
                            startY: parseFloat(line.coordinates.startY) || 0,
                            endX: parseFloat(line.coordinates.endX) || 0,
                            endY: parseFloat(line.coordinates.endY) || 0,
                            color: line.color || [255, 0, 0],
                            label: line.label || `Đường thẳng`,
                            detect_objects: line.detect_objects || ['Person'],
                            direction: line.direction || 'both',
                            rule_name: line.rule_name || '',
                            instance_uuid: instanceUuid
                        })));
                    }

                    // Tải zones
                    if (data.data.zones && Array.isArray(data.data.zones)) {
                        zones.push(...data.data.zones.map(zone => {
                            const shapeId = generateShapeId();
                            if (zone.type === 'rect') {
                                return {
                                    id: shapeId,
                                    type: 'rect',
                                    startX: parseFloat(zone.coordinates.startX) || 0,
                                    startY: parseFloat(zone.coordinates.startY) || 0,
                                    width: parseFloat(zone.coordinates.width) || 0,
                                    height: parseFloat(zone.coordinates.height) || 0,
                                    color: zone.color || [255, 0, 0],
                                    label: zone.label || `Hình chữ nhật`,
                                    detect_objects: zone.detect_objects || ['Person'],
                                    direction: zone.direction || 'both',
                                    rule_name: zone.rule_name || '',
                                    instance_uuid: instanceUuid
                                };
                            } else if (zone.type === 'poly') {
                                return {
                                    id: shapeId,
                                    type: 'poly',
                                    points: zone.coordinates.map(point => ({
                                        x: parseFloat(point.x) || 0,
                                        y: parseFloat(point.y) || 0
                                    })),
                                    color: zone.color || [255, 0, 0],
                                    label: zone.label || `Đa giác`,
                                    detect_objects: zone.detect_objects || ['Person'],
                                    direction: zone.direction || 'both',
                                    rule_name: zone.rule_name || '',
                                    instance_uuid: instanceUuid
                                };
                            }
                            return null;
                        }).filter(Boolean));
                    }

                    // Cập nhật shapes hiển thị dựa trên currentRuleType
                    shapes = currentRuleType === 'Line Crossing' ? [...lines] : [...zones];

                    redrawAll();
                    updateShapesList();
                }
            })
            .catch(error => {
                console.error('Lỗi khi tải dữ liệu:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể tải dữ liệu từ server: ' + error.message
                });
            });
    };

    // Save shapes to server
    const saveShapesToServer = (instanceId) => {
        // Gửi cả lines và zones
        const shapesToSave = [...lines, ...zones];

        fetch(`/cvedixrt/instances/${instanceId}/shapes`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ shapes: shapesToSave })
        })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => Promise.reject(err));
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: 'Đã lưu dữ liệu thành công',
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    throw new Error(data.message || 'Không thể lưu dữ liệu');
                }
            })
            .catch(error => {
                console.error('Lỗi khi lưu dữ liệu:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Lỗi',
                    text: 'Không thể lưu dữ liệu: ' + error.message
                });
            });
    };

    // Initialize drawing tool
    const init = () => {
        if (!initElements()) return;
        initEventListeners();

        const style = document.createElement('style');
        style.innerHTML = `
            .draw-tool-btn {
                background: white;
                color: #3b82f6;
                border: 1.5px solid #3b82f6;
            }
            .draw-tool-btn.active, .draw-tool-btn:active {
                background: #3b82f6 !important;
                color: white !important;
                border: 1.5px solid #3b82f6 !important;
            }
            .draw-tool-btn:disabled {
                background: #e5e7eb !important;
                color: #9ca3af !important;
                border: 1.5px solid #d1d5db !important;
                cursor: not-allowed;
            }
            .inline-edit-input {
                background: white;
                border: 1px solid #ddd;
                border-radius: 3px;
                padding: 2px 4px;
                font-size: 12px;
                width: 150px;
            }
            #canvasOverlay:hover {
                cursor: ${editMode ? 'default' : 'crosshair'};
            }
        `;
        document.head.appendChild(style);

        setActiveMode(currentRuleType === 'Line Crossing' ? 'line' : 'rect');
        updateDrawingModes();
    };

    // Public methods
    return {
        open: function (instanceId, uuid, options = {}) {
            instanceUuid = uuid;
            currentRuleType = options.rule_type || 'Line Crossing';
            detectObjects = options.detect_objects || ['Person'];
            ruleName = options.rule_name || '';
            shapeIdCounter = 0;

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: { confirmButton: 'hidden', popup: 'swal-wide-popup' },
                buttonsStyling: false,
                showConfirmButton: false,
                showCloseButton: true,
                width: '80%'
            });

            const drawingToolHtml = `
                <div class="flex flex-col max-w-full">
                    <div class="bg-white rounded-md shadow p-4 mb-4">
                        <div class="flex flex-wrap gap-2 mb-2">
                            <button id="lineBtn" class="draw-tool-btn px-3 py-2 rounded transition-colors" style="display: none;">Draw Line</button>
                            <button id="rectBtn" class="draw-tool-btn px-3 py-2 rounded transition-colors" style="display: none;">Draw Rect</button>
                            <button id="polyBtn" class="draw-tool-btn px-3 py-2 rounded transition-colors" style="display: none;">Draw Poly</button>
                            <button id="closePolyBtn" class="px-3 py-2 bg-blue-500 text-black rounded hover:bg-blue-600 transition-colors" style="display: none;" disabled>Close Poly</button>
                            <button id="undoBtn" class="px-3 py-2 bg-blue-500 text-black rounded hover:bg-blue-600 transition-colors">⟲ Undo</button>
                            <button id="redoBtn" class="px-3 py-2 bg-blue-500 text-black rounded hover:bg-blue-600 transition-colors">⟳ Redo</button>
                            <button id="clearBtn" class="px-3 py-2 bg-blue-500 text-black rounded hover:bg-blue-600 transition-colors">Clear</button>
                        </div>
                        <div class="flex flex-wrap gap-2 mb-2 mt-3">
                            <label class="flex items-center gap-2">
                                <span>Color:</span>
                                <input type="color" id="colorPicker" value="#ff0000" class="w-8 h-8 border-0">
                            </label>
                            <label class="flex items-center gap-2 ml-4">
                                <span>Label:</span>
                                <input type="text" id="labelInput" placeholder="Enter label name" class="px-2 py-1 border border-gray-300 rounded">
                            </label>
                            <label class="flex items-center gap-2 ml-4">
                                <span>Direction:</span>
                                <select id="directionSelect" class="px-2 py-1 border border-gray-300 rounded">
                                    <option value="in">In</option>
                                    <option value="out">Out</option>
                                    <option value="both" selected>Both</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="relative w-full bg-black" style="height: 400px;">
                        <div id="videoWrapper" class="w-full h-full relative">
                            <video id="videoElement" class="absolute top-0 left-0 w-full h-full object-contain" style="z-index:0;" autoplay loop muted playsinline>
                                <source src="https://archive.org/download/BigBuckBunny_124/Content/big_buck_bunny_720p_surround.mp4" type="video/mp4">
                            </video>
                            <canvas id="canvasOverlay" class="absolute top-0 left-0" style="z-index:10; pointer-events: auto;"></canvas>
                        </div>
                    </div>
                    <div class="mt-4 p-3 border border-gray-200 rounded max-h-40 overflow-y-auto">
                        <h3 class="font-semibold mb-2">List of Drawn Shapes: </h3>
                        <ul id="shapesList" class="list-disc pl-5"></ul>
                    </div>
                    <div class="flex justify-end gap-3 mt-4">
                        <button id="saveBtn" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Save</button>
                        <button id="cancelBtn" class="px-6 py-2 bg-gray-300 text-gray-700 rounded hover:bg-gray-400">Cancel</button>
                    </div>
                </div>
            `;

            swalWithBootstrapButtons.fire({
                title: '',
                html: drawingToolHtml,
                didOpen: () => {
                    const style = document.createElement('style');
                    style.innerHTML = `
                        .swal-wide-popup { max-width: 900px !important; }
                        .swal2-html-container { overflow-x: hidden; }
                    `;
                    document.head.appendChild(style);

                    setTimeout(() => {
                        init();
                        loadShapesFromServer(instanceId);
                        document.getElementById('saveBtn').addEventListener('click', () => {
                            saveShapesToServer(instanceId);
                        });
                        document.getElementById('cancelBtn').addEventListener('click', () => Swal.close());
                    }, 100);
                }
            });
        },
        getShapes: () => [...shapes],
        loadShapes: loadShapesFromServer,
        saveShapes: saveShapesToServer,
    };
})();

window.DrawingTool = DrawingTool;
