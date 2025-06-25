/**
 * Drawing Tool Module
 * Handles canvas drawing functionality for analytics rules
 */
let DrawingTool = (function () {
    let canvas, ctx, videoElement;
    let isDrawing = false;
    let isResizing = false;
    let resizeHandle = null;
    const handleSize = 8;
    let currentMode = 'line';
    let editMode = false;
    let selectedShape = null;
    let startX, startY, currentX, currentY;
    let points = [];
    window.shapes = [];
    let lines = [];
    let zones = [];
    let redoShapes = [];
    let instanceUuid = null;
    let currentColor = [255, 0, 0];
    let currentLabel = '';
    let currentRuleType = 'Line Crossing';
    let detectObjects = ['Person'];
    let ruleName = '';
    let direction = 'both';
    const defaultWidth = 1;
    let shapeIdCounter = 0;
    window.tempShapes = [];
    let selectedRule = null;

    const generateShapeId = () => {
        return `shape_${instanceUuid}_${shapeIdCounter++}`;
    };

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

    const isPointNearShape = (x, y, shape, tolerance = 15) => {
        if (shape.type === 'line') {
            const dx = shape.endX - shape.startX;
            const dy = shape.endY - shape.startY;
            const length = Math.sqrt(dx * dx + dy * dy);
            const midX = (shape.startX + shape.endX) / 2;
            const midY = (shape.startY + shape.endY) / 2;

            const rotation = shape.rotation || 0;
            const cosR = Math.cos(rotation);
            const sinR = Math.sin(rotation);

            const relX = x - midX;
            const relY = y - midY;
            const rotatedX = relX * cosR + relY * sinR;
            const rotatedY = -relX * sinR + relY * cosR;

            const handles = [
                {x: shape.startX, y: shape.startY, handle: 'start'},
                {x: shape.endX, y: shape.endY, handle: 'end'},
                {x: midX + (length / 2 + 20) * cosR, y: midY + (length / 2 + 20) * sinR, handle: 'rotate'}
            ];

            for (const h of handles) {
                if (Math.hypot(x - h.x, y - h.y) <= handleSize) {
                    return {isHandle: true, handle: h.handle};
                }
            }

            if (length === 0) {
                return {isHandle: false, inside: Math.hypot(x - shape.startX, y - shape.startY) <= tolerance};
            }

            const t = Math.max(0, Math.min(1, ((x - shape.startX) * dx + (y - shape.startY) * dy) / (length * length)));
            const projection = {
                x: shape.startX + t * dx,
                y: shape.startY + t * dy
            };
            const distance = Math.hypot(x - projection.x, y - projection.y);
            return {isHandle: false, inside: distance <= tolerance};
        } else if (shape.type === 'rect') {
            const handles = [
                {x: shape.startX, y: shape.startY, handle: 'top-left'},
                {x: shape.startX + shape.width, y: shape.startY, handle: 'top-right'},
                {x: shape.startX, y: shape.startY + shape.height, handle: 'bottom-left'},
                {x: shape.startX + shape.width, y: shape.startY + shape.height, handle: 'bottom-right'}
            ];

            for (const h of handles) {
                if (Math.abs(x - h.x) <= handleSize && Math.abs(y - h.y) <= handleSize) {
                    return {isHandle: true, handle: h.handle};
                }
            }

            return {
                isHandle: false,
                inside: x >= shape.startX && x <= shape.startX + shape.width &&
                    y >= shape.startY && y <= shape.startY + shape.height
            };
        } else if (shape.type === 'poly') {
            for (let i = 0; i < shape.points.length; i++) {
                const point = shape.points[i];
                if (Math.hypot(x - point.x, y - point.y) <= handleSize) {
                    return {isHandle: true, handle: `point_${i}`};
                }
            }

            const center = shape.points.reduce((acc, p) => ({
                x: acc.x + p.x,
                y: acc.y + p.y
            }), {x: 0, y: 0});
            center.x /= shape.points.length;
            center.y /= shape.points.length;

            if (Math.hypot(x - center.x, y - center.y) <= handleSize) {
                return {isHandle: true, handle: 'scale'};
            }

            let inside = false;
            for (let i = 0, j = shape.points.length - 1; i < shape.points.length; j = i++) {
                if (
                    ((shape.points[i].y > y) !== (shape.points[j].y > y)) &&
                    (x < (shape.points[j].x - shape.points[i].x) * (y - shape.points[i].y) / (shape.points[j].y - shape.points[i].y) + shape.points[i].x)
                ) {
                    inside = !inside;
                }
            }
            return {isHandle: false, inside};
        }
        return {isHandle: false, inside: false};
    };

    const findShapeAtPoint = (x, y) => {
        for (let i = shapes.length - 1; i >= 0; i--) {
            const pointInfo = isPointNearShape(x, y, shapes[i]);
            if (pointInfo.isHandle || pointInfo.inside) {
                return {shape: shapes[i], pointInfo};
            }
        }
        return null;
    };

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

    const updateDrawingModes = () => {
        const lineBtn = document.getElementById('lineBtn');
        const rectBtn = document.getElementById('rectBtn');
        const polyBtn = document.getElementById('polyBtn');
        const closePolyBtn = document.getElementById('closePolyBtn');
        const editBtn = document.getElementById('editBtn');

        [lineBtn, rectBtn, polyBtn, closePolyBtn, editBtn].forEach(btn => {
            if (btn) btn.style.display = 'none';
        });

        if (currentRuleType.toLowerCase() === 'line_crossing') {
            if (lineBtn) lineBtn.style.display = 'inline-block';
            if (editBtn) editBtn.style.display = 'inline-block';
            setActiveMode('line');
        } else {
            if (lineBtn) lineBtn.style.display = 'inline-block';
            if (rectBtn) rectBtn.style.display = 'inline-block';
            if (polyBtn) polyBtn.style.display = 'inline-block';
            if (editBtn) editBtn.style.display = 'inline-block';
            if (closePolyBtn) {
                closePolyBtn.style.display = currentMode === 'poly' ? 'inline-block' : 'none';
            }
            setActiveMode(currentMode === 'line' ? 'rect' : currentMode);
        }

        updateClosePolyButton();
        redrawAll();
        updateShapesList();
    };

    const initEventListeners = function () {
        const lineBtn = document.getElementById('lineBtn');
        const rectBtn = document.getElementById('rectBtn');
        const polyBtn = document.getElementById('polyBtn');
        const closePolyBtn = document.getElementById('closePolyBtn');
        const editBtn = document.getElementById('editBtn');
        const undoBtn = document.getElementById('undoBtn');
        const redoBtn = document.getElementById('redoBtn');
        const clearBtn = document.getElementById('clearBtn');
        const colorPicker = document.getElementById('colorPicker');
        const labelInput = document.getElementById('labelInput');
        const directionSelect = document.getElementById('directionSelect');

        if (!undoBtn || !redoBtn || !clearBtn || !colorPicker || !labelInput || !directionSelect || !editBtn) {
            console.error('One or more UI elements not found');
            return;
        }

        if (lineBtn) {
            lineBtn.addEventListener('click', () => {
                setActiveMode('line');
                points = [];
                updateClosePolyButton();
                redrawAll();
            });
        }
        if (rectBtn) {
            rectBtn.addEventListener('click', () => {
                setActiveMode('rect');
                points = [];
                updateClosePolyButton();
            });
        }
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
                    points.push({...points[0]});
                    finishPolygon();
                }
            });
        }
        if (editBtn) {
            editBtn.addEventListener('click', () => {
                setActiveMode('edit');
                points = [];
                updateClosePolyButton();
                redrawAll();
            });
        }

        undoBtn.addEventListener('click', () => {
            if (shapes.length > 0) {
                redoShapes.push(shapes.pop());
                lines = shapes.filter(s => s.type === 'line');
                zones = shapes.filter(s => s.type !== 'line');
                redrawAll();
                updateShapesList();
            }
        });
        redoBtn.addEventListener('click', () => {
            if (redoShapes.length > 0) {
                shapes.push(redoShapes.pop());
                lines = shapes.filter(s => s.type === 'line');
                zones = shapes.filter(s => s.type !== 'line');
                redrawAll();
                updateShapesList();
            }
        });
        clearBtn.addEventListener('click', () => {
            shapes = [];
            lines = [];
            zones = [];
            redoShapes = [];
            points = [];
            tempShapes = [];
            window.tempShapesToSave = null;
            redrawAll();
            updateShapesList();
            updateClosePolyButton();
        });

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

        canvas.addEventListener('mousedown', startDrawing);
        canvas.addEventListener('mousemove', draw);
        canvas.addEventListener('mouseup', endDrawing);
        canvas.addEventListener('dblclick', finishPolygon);

        canvas.addEventListener('touchstart', handleTouchStart);
        canvas.addEventListener('touchmove', handleTouchMove);
        canvas.addEventListener('touchend', handleTouchEnd);

        videoElement.addEventListener('loadeddata', resizeCanvas);
        window.addEventListener('resize', resizeCanvas);
        videoElement.onloadedmetadata = () => {
            videoElement.play();
            resizeCanvas();
        };
    };

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

    const setActiveMode = function (mode) {
        if (currentRuleType === 'Line Crossing' && mode !== 'line' && mode !== 'edit') {
            return;
        }

        currentMode = mode;
        editMode = mode === 'edit';
        selectedShape = null;

        const buttons = [
            document.getElementById('lineBtn'),
            document.getElementById('rectBtn'),
            document.getElementById('polyBtn'),
            document.getElementById('editBtn')
        ].filter(Boolean);

        buttons.forEach(btn => btn.classList.remove('active'));
        if (mode === 'line' && buttons[0]) buttons[0].classList.add('active');
        else if (mode === 'rect' && buttons[1]) buttons[1].classList.add('active');
        else if (mode === 'poly' && buttons[2]) buttons[2].classList.add('active');
        else if (mode === 'edit' && buttons[3]) buttons[3].classList.add('active');

        const closePolyBtn = document.getElementById('closePolyBtn');
        if (closePolyBtn) {
            closePolyBtn.style.display = mode === 'poly' ? 'inline-block' : 'none';
            closePolyBtn.disabled = mode !== 'poly' || points.length < 2;
        }

        points = [];
        redrawAll();
        canvas.style.cursor = editMode ? 'default' : 'crosshair';
    };

    const updateClosePolyButton = () => {
        const closePolyBtn = document.getElementById('closePolyBtn');
        if (closePolyBtn) {
            closePolyBtn.disabled = currentMode !== 'poly' || points.length < 2;
        }
    };

    const resizeCanvas = () => {
        const wrapper = document.getElementById('videoWrapper');
        if (!wrapper || wrapper.offsetWidth === 0 || wrapper.offsetHeight === 0) {
            setTimeout(resizeCanvas, 100);
            return;
        }
        canvas.width = wrapper.offsetWidth;
        canvas.height = wrapper.offsetHeight;
        canvas.style.width = wrapper.offsetWidth + 'px';
        canvas.style.height = wrapper.offsetHeight + 'px';
        videoElement.style.width = wrapper.offsetWidth + 'px';
        videoElement.style.height = wrapper.offsetHeight + 'px';
        redrawAll();
    };

    const startDrawing = (e) => {
        const rect = canvas.getBoundingClientRect();

        if (rect.width === 0 || rect.height === 0) {
            console.warn("Canvas dimensions are zero, cannot start drawing.");
            return;
        }
        startX = e.clientX - rect.left;
        startY = e.clientY - rect.top;
        currentX = startX;
        currentY = startY;

        if (editMode) {
            const result = findShapeAtPoint(startX, startY);
            if (result) {
                selectedShape = result.shape;
                const pointInfo = result.pointInfo;
                document.getElementById('colorPicker').value = rgbToHex(selectedShape.color);
                document.getElementById('labelInput').value = selectedShape.label;
                currentColor = [...selectedShape.color];
                currentLabel = selectedShape.label;
                direction = selectedShape.direction || 'both';
                document.getElementById('directionSelect').value = direction;

                isDrawing = true;

                if (pointInfo.isHandle) {
                    isResizing = true;
                    resizeHandle = pointInfo.handle;
                } else if (pointInfo.inside) {
                    isResizing = false;
                    if (selectedShape.type === 'line') {
                        selectedShape.offsetX = startX - selectedShape.startX;
                        selectedShape.offsetY = startY - selectedShape.startY;
                    } else if (selectedShape.type === 'rect') {
                        selectedShape.offsetX = startX - selectedShape.startX;
                        selectedShape.offsetY = startY - selectedShape.startY;
                    } else if (selectedShape.type === 'poly') {
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
            points.push({x: startX, y: startY, color: rgbToHex(currentColor)});
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

    const draw = (e) => {
        if (!isDrawing) return;

        const rect = canvas.getBoundingClientRect();

        if (rect.width === 0 || rect.height === 0) {
            console.warn("Canvas dimensions are zero, cannot draw.");
            return;
        }

        currentX = e.clientX - rect.left;
        currentY = e.clientY - rect.top;

        if (editMode && selectedShape) {
            const deltaX = currentX - startX;
            const deltaY = currentY - startY;

            if (isResizing) {
                if (selectedShape.type === 'line') {
                    if (resizeHandle === 'start') {
                        selectedShape.startX = currentX;
                        selectedShape.startY = currentY;
                    } else if (resizeHandle === 'end') {
                        selectedShape.endX = currentX;
                        selectedShape.endY = currentY;
                    } else if (resizeHandle === 'rotate') {
                        const midX = (selectedShape.startX + selectedShape.endX) / 2;
                        const midY = (selectedShape.startY + selectedShape.endY) / 2;
                        const newAngle = Math.atan2(currentY - midY, currentX - midX);
                        selectedShape.rotation = newAngle - Math.PI / 2;
                    }
                } else if (selectedShape.type === 'rect') {
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
                } else if (selectedShape.type === 'poly') {
                    if (resizeHandle.startsWith('point_')) {
                        const index = parseInt(resizeHandle.split('_')[1]);
                        selectedShape.points[index].x = currentX;
                        selectedShape.points[index].y = currentY;
                    } else if (resizeHandle === 'scale') {
                        const center = selectedShape.points.reduce((acc, p) => ({
                            x: acc.x + p.x,
                            y: acc.y + p.y
                        }), {x: 0, y: 0});
                        center.x /= selectedShape.points.length;
                        center.y /= selectedShape.points.length;
                        const scale = Math.hypot(currentX - center.x, currentY - center.y) /
                            Math.hypot(startX - center.x, startY - center.y);
                        selectedShape.points = selectedShape.points.map(p => ({
                            x: center.x + (p.x - center.x) * scale,
                            y: center.y + (p.y - center.y) * scale
                        }));
                    }
                }
            } else {
                if (selectedShape.type === 'line') {
                    selectedShape.startX = currentX - selectedShape.offsetX;
                    selectedShape.startY = currentY - selectedShape.offsetY;
                    selectedShape.endX = currentX - selectedShape.offsetX + (selectedShape.endX - selectedShape.startX);
                    selectedShape.endY = currentY - selectedShape.offsetY + (selectedShape.endY - selectedShape.startY);
                } else if (selectedShape.type === 'rect') {
                    selectedShape.startX = currentX - selectedShape.offsetX;
                    selectedShape.startY = currentY - selectedShape.offsetY;
                } else if (selectedShape.type === 'poly') {
                    selectedShape.points = selectedShape.points.map(point => ({
                        x: point.x + deltaX,
                        y: point.y + deltaY
                    }));
                }
            }

            if (selectedShape.type === 'line') {
                const index = lines.findIndex(s => s.id === selectedShape.id);
                if (index !== -1) lines[index] = {...selectedShape};
            } else {
                const index = zones.findIndex(s => s.id === selectedShape.id);
                if (index !== -1) zones[index] = {...selectedShape};
            }

            window.shapes = [...lines, ...zones].filter((shape, index, self) =>
                index === self.findIndex(s => s.id === shape.id)
            );

            startX = currentX;
            startY = currentY;
            redrawAll();
            // return;
        } else if (currentMode === 'poly') {
            redrawAll();

            ctx.beginPath();
            ctx.strokeStyle = rgbToHex(currentColor);
            ctx.lineWidth = defaultWidth;
            ctx.setLineDash([]);

            // Draw all points
            points.forEach((point, index) => {
                ctx.beginPath();
                ctx.arc(point.x, point.y, 3, 0, Math.PI * 2);
                ctx.fill();

                if (index > 0) {
                    ctx.beginPath();
                    ctx.moveTo(points[index - 1].x, points[index - 1].y);
                    ctx.lineTo(point.x, point.y);
                    ctx.stroke();
                }
            });

            // Vẽ tất cả các điểm và cạnh của polygon
            if (points.length > 0) {
                ctx.beginPath();
                ctx.moveTo(points[0].x, points[0].y);
                for (let i = 1; i < points.length; i++) {
                    ctx.lineTo(points[i].x, points[i].y);
                }
                // Vẽ cạnh tạm thời từ điểm cuối đến vị trí chuột
                ctx.lineTo(currentX, currentY);
                ctx.stroke();

                // Vẽ các điểm
                points.forEach(point => {
                    ctx.beginPath();
                    ctx.arc(point.x, point.y, 3, 0, Math.PI * 2);
                    ctx.fillStyle = rgbToHex(currentColor);
                    ctx.fill();
                });
            }
        } else if (currentMode === 'line' || currentMode === 'rect') {
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

    const endDrawing = (e) => {
        if (!isDrawing) return;
        isDrawing = false;
        isResizing = false;
        resizeHandle = null;

        const rect = canvas.getBoundingClientRect();
        if (rect.width === 0 || rect.height === 0) {
            console.warn('Canvas has invalid dimensions');
            return;
        }
        const endX = e.clientX - rect.left;
        const endY = e.clientY - rect.top;

        if (editMode && selectedShape) {
            selectedShape.color = [...currentColor];
            selectedShape.label = currentLabel || 'Line';
            selectedShape.direction = direction;

            if (selectedShape.type === 'line') {
                const index = lines.findIndex(s => s.id === selectedShape.id);
                if (index !== -1) {
                    lines[index] = {...selectedShape};
                } else {
                    lines.push({...selectedShape});
                }
            } else {
                const index = zones.findIndex(s => s.id === selectedShape.id);
                if (index !== -1) {
                    zones[index] = {...selectedShape};
                } else {
                    zones.push({...selectedShape});
                }
            }

            shapes = [...lines, ...zones].filter((shape, index, self) =>
                index === self.findIndex(s => s.id === shape.id)
            );

            updateShapesList();
            redrawAll();
            return;
        }

        if (currentMode === 'line') {
            const count = lines.filter(s => s.type === 'line').length + 1;
            const newShape = {
                id: generateShapeId(),
                type: 'line',
                startX, startY, endX, endY,
                rotation: 0,
                color: [...currentColor],
                label: `Line ${count}`,
                detect_objects: [...detectObjects],
                direction: direction,
                rule_name: ruleName,
                instance_uuid: instanceUuid
            };
            lines.push(newShape);
            shapes.push(newShape);
        } else if (currentMode === 'rect') {
            const count = zones.filter(s => s.type === 'rect').length + 1;
            const newShape = {
                id: generateShapeId(),
                type: 'rect',
                startX, startY,
                width: endX - startX,
                height: endY - startY,
                color: [...currentColor],
                label: `Rect ${count}`,
                detect_objects: [...detectObjects],
                direction: direction,
                rule_name: ruleName,
                instance_uuid: instanceUuid
            };
            zones.push(newShape);
            shapes.push(newShape);
        }

        currentColor = generateRandomColor();
        document.getElementById('colorPicker').value = rgbToHex(currentColor);

        updateShapesList();
        redrawAll();
    };

    const finishPolygon = () => {
        if (currentMode !== 'poly' || points.length < 3) return;

        const count = shapes.filter(s => s.type === 'poly').length + 1;
        const newShape = {
            id: generateShapeId(),
            type: 'poly',
            points: [...points],
            color: [...currentColor],
            label: `Polygon ${count}`,
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
                if (shape.type === 'line') {
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

    const updateShapesList = () => {
        const shapesList = document.getElementById('shapesList');
        shapesList.innerHTML = '';

        const filteredShapes = [...new Set([...lines, ...zones].map(s => s.id))].map(id =>
            shapes.find(s => s.id === id)
        ).filter(Boolean);

        filteredShapes.forEach(shape => {
            const li = document.createElement('li');
            li.className = 'flex items-center justify-between py-1 px-2 rounded hover:bg-gray-200 mb-1';

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
            labelSpan.textContent = shape.label;
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
                direction = shape.direction;
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
                lines = lines.filter(s => s.id !== shape.id);
                zones = zones.filter(s => s.id !== shape.id);
                window.shapes = shapes.filter(s => s.id !== shape.id);
                window.tempShapes = tempShapes.filter(s => s.id !== shape.id);
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

    const redrawAll = () => {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        if (!shapes.length) {
            return;
        }

        shapes.forEach(shape => {
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
                const rotation = shape.rotation || 0;
                const midX = (shape.startX + shape.endX) / 2;
                const midY = (shape.startY + shape.endY) / 2;

                ctx.save();
                ctx.translate(midX, midY);
                ctx.rotate(rotation);
                ctx.moveTo(shape.startX - midX, shape.startY - midY);
                ctx.lineTo(shape.endX - midX, shape.endY - midY);
                ctx.stroke();
                ctx.restore();
            } else if (shape.type === 'rect') {
                ctx.rect(shape.startX, shape.startY, shape.width, shape.height);
                ctx.fillStyle = rgbToRgba(shape.color, 0.12);
                ctx.fill();
                ctx.stroke();
            } else if (shape.type === 'poly' && shape.points.length > 0) {
                ctx.moveTo(shape.points[0].x, shape.points[0].y);
                shape.points.slice(1).forEach(point => ctx.lineTo(point.x, point.y));
                ctx.closePath();
                ctx.fillStyle = rgbToRgba(shape.color, 0.12);
                ctx.fill();
                ctx.stroke();
            }

            if (selectedShape && selectedShape.id === shape.id) {
                let handles = [];
                if (shape.type === 'line') {
                    const midX = (shape.startX + shape.endX) / 2;
                    const midY = (shape.startY + shape.endY) / 2;
                    const length = Math.sqrt((shape.endX - shape.startX) ** 2 + (shape.endY - shape.startY) ** 2);
                    const rotation = shape.rotation || 0;
                    const cosR = Math.cos(rotation);
                    const sinR = Math.sin(rotation);
                    handles = [
                        {x: shape.startX, y: shape.startY, handle: 'start'},
                        {x: shape.endX, y: shape.endY, handle: 'end'},
                        {x: midX + (length / 2 + 20) * cosR, y: midY + (length / 2 + 20) * sinR, handle: 'rotate'}
                    ];
                } else if (shape.type === 'rect') {
                    handles = [
                        {x: shape.startX, y: shape.startY, handle: 'top-left'},
                        {x: shape.startX + shape.width, y: shape.startY, handle: 'top-right'},
                        {x: shape.startX, y: shape.startY + shape.height, handle: 'bottom-left'},
                        {x: shape.startX + shape.width, y: shape.startY + shape.height, handle: 'bottom-right'}
                    ];
                } else if (shape.type === 'poly') {
                    handles = shape.points.map((point, index) => ({
                        x: point.x,
                        y: point.y,
                        handle: `point_${index}`
                    }));
                    const center = shape.points.reduce((acc, p) => ({
                        x: acc.x + p.x,
                        y: acc.y + p.y
                    }), {x: 0, y: 0});
                    center.x /= shape.points.length;
                    center.y /= shape.points.length;
                    handles.push({x: center.x, y: center.y, handle: 'scale'});
                }

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
                    ctx.save();
                    ctx.translate(midX, midY);
                    ctx.rotate(shape.rotation || 0);
                    ctx.fillText(shape.label, 5, -5);
                    ctx.restore();
                } else if (shape.type === 'rect') {
                    ctx.fillText(shape.label, shape.startX + 5, shape.startY - 5);
                } else if (shape.type === 'poly' && shape.points.length > 0) {
                    const {x: centerX, y: centerY} = shape.points.reduce((acc, p) => ({
                        x: acc.x + p.x,
                        y: acc.y + p.y
                    }), {x: 0, y: 0});
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

    const saveShapesToServer = (instanceId) => {

        const drawingObjects = shapes.map(shape => {
            const baseShape = {
                type: shape.type,
                color: shape.color,
                label: shape.label
            };

            if (shape.type === 'line') {
                return {
                    ...baseShape,
                    startX: shape.startX,
                    startY: shape.startY,
                    endX: shape.endX,
                    endY: shape.endY,
                    rotation: shape.rotation || 0
                };
            } else if (shape.type === 'rect') {
                return {
                    ...baseShape,
                    startX: shape.startX,
                    startY: shape.startY,
                    width: shape.width,
                    height: shape.height
                };
            } else if (shape.type === 'poly') {
                return {
                    ...baseShape,
                    points: shape.points.map(point => ({x: point.x, y: point.y}))
                };
            }
            return null;
        }).filter(Boolean);

        const shapesToSave = {
            uuid: instanceUuid,
            name: ruleName,
            detected_object: detectObjects,
            rule_type: currentRuleType.toLowerCase().replace(' ', '_'),
            drawing_object: drawingObjects,
            direction: direction,
            instance_id: instanceId
        };

        window.tempShapesToSave = shapesToSave;
        window.tempShapes = shapesToSave;

        Swal.fire({
            icon: 'success',
            title: 'Thành công',
            text: 'Đã lưu dữ liệu tạm thời',
            timer: 1200,
            showConfirmButton: false
        });
    };

    const processShapesFromServer = (instanceRule) => {

        if (!instanceRule) {
            window.shapes = [];
            lines = [];
            zones = [];
            selectedRule = null;
            window.tempShapes = [];
            redrawAll();
            updateShapesList();
            updateDrawingModes();
            return;
        }

        selectedRule = {...instanceRule};
        window.shapes = [];
        lines = [];
        zones = [];

        // Xử lý drawing_object
        if (instanceRule.drawing_object && Array.isArray(instanceRule.drawing_object)) {

            instanceRule.drawing_object.forEach(shape => {
                const shapeId = generateShapeId();
                const baseShape = {
                    id: shapeId,
                    type: shape.type,
                    color: shape.color,
                    label: shape.label,
                    detect_objects: instanceRule.detected_object,
                    direction: instanceRule.direction,
                    rule_name: instanceRule.name,
                    instance_uuid: instanceRule.uuid
                };

                if (shape.type === 'line') {
                    const lineShape = {
                        ...baseShape,
                        startX: parseFloat(shape.startX),
                        startY: parseFloat(shape.startY),
                        endX: parseFloat(shape.endX),
                        endY: parseFloat(shape.endY),
                        rotation: parseFloat(shape.rotation)
                    };
                    // Kiểm tra tính hợp lệ của line
                    if (lineShape.startX !== lineShape.endX || lineShape.startY !== lineShape.endY) {
                        lines.push(lineShape);
                        shapes.push(lineShape);
                    } else {
                        console.warn('Bỏ qua line không hợp lệ (điểm đầu và cuối trùng nhau):', lineShape);
                    }
                } else if (shape.type === 'rect') {
                    const rectShape = {
                        ...baseShape,
                        startX: parseFloat(shape.startX),
                        startY: parseFloat(shape.startY),
                        width: parseFloat(shape.width),
                        height: parseFloat(shape.height)
                    };
                    // Kiểm tra tính hợp lệ của rect
                    if (rectShape.width > 0 && rectShape.height > 0) {
                        zones.push(rectShape);
                        shapes.push(rectShape);
                    } else {
                        console.warn('Bỏ qua rect không hợp lệ (width hoặc height không hợp lệ):', rectShape);
                    }
                } else if (shape.type === 'poly') {
                    const polyShape = {
                        ...baseShape,
                        points: Array.isArray(shape.points) ? shape.points.map(point => ({
                            x: parseFloat(point.x),
                            y: parseFloat(point.y)
                        })) : []
                    };
                    if (polyShape.points.length >= 3) {
                        zones.push(polyShape);
                        shapes.push(polyShape);
                    } else {
                        console.warn('Bỏ qua poly không hợp lệ (số điểm < 3):', polyShape);
                    }
                }
            });

            // Loại bỏ các shapes trùng lặp
            window.shapes = [...lines, ...zones].filter((shape, index, self) =>
                index === self.findIndex(s => s.id === shape.id)
            );
        } else {
            console.warn('Không có drawing_object hoặc không phải mảng:', instanceRule.drawing_object);
        }

        window.tempShapes = [...shapes];
    };

    const renderShapesForRule = () => {
        instanceUuid = instanceUuid || null;
        ruleName = ruleName || '';
        detectObjects = detectObjects || ['Person'];
        direction = direction || 'both';

        if (selectedRule) {
            instanceUuid = selectedRule.uuid;
            ruleName = selectedRule.name;
            detectObjects = selectedRule.detected_object
            currentRuleType = selectedRule.rule_type
            direction = selectedRule.direction;
        }

        // Cập nhật giao diện người dùng
        document.getElementById('directionSelect').value = direction;
        document.getElementById('labelInput').value = '';
        document.getElementById('colorPicker').value = rgbToHex(currentColor);

        // Đảm bảo canvas được resize trước khi vẽ
        resizeCanvas();

        // Vẽ lại tất cả shapes
        redrawAll();
        updateShapesList();
        updateDrawingModes();
    };

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
            #saveBtn {
                background-color: #4CAF50;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s;
            }
            #saveBtn:hover {
                background-color: #45a049;
            }
            #cancelBtn {
                background-color: #f44336;
                color: white;
                border: none;
                padding: 8px 16px;
                border-radius: 4px;
                cursor: pointer;
                transition: background-color 0.3s;
            }
            #cancelBtn:hover {
                background-color: #da190b;
            }
        `;
        document.head.appendChild(style);

        // Đợi video tải xong trước khi cho phép vẽ
        if (videoElement.readyState >= 3) {
            resizeCanvas();
            if (tempShapes.length > 0) {
                window.shapes = [...tempShapes];
                lines = shapes.filter(s => s.type === 'line');
                zones = shapes.filter(s => s.type !== 'line');
                redrawAll();
                updateShapesList();
            }
            setActiveMode(currentRuleType.toLowerCase() === 'line_crossing' ? 'line' : 'rect');
            updateDrawingModes();
        } else {
            videoElement.addEventListener('loadeddata', () => {
                resizeCanvas();
                if (tempShapes.length > 0) {
                    window.shapes = [...tempShapes];
                    lines = shapes.filter(s => s.type === 'line');
                    zones = shapes.filter(s => s.type !== 'line');
                    redrawAll();
                    updateShapesList();
                }
                setActiveMode(currentRuleType.toLowerCase() === 'line_crossing' ? 'line' : 'rect');
                updateDrawingModes();
            }, {once: true});
        }
    };

    return {
        open: function (instanceId, uuid, options = {}) {
            instanceUuid = uuid;
            currentRuleType = options.rule_type || 'line_crossing';
            detectObjects = options.detect_objects || ['Person'];
            ruleName = options.rule_name || '';
            shapeIdCounter = 0;

            const swalWithBootstrapButtons = Swal.mixin({
                customClass: {confirmButton: 'hidden', popup: 'swal-wide-popup'},
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
                            <button id="closePolyBtn" class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors" style="display: none;" disabled>Close Poly</button>
                            <button id="editBtn" class="draw-tool-btn px-3 py-2 rounded transition-colors">Edit</button>
                            <button id="undoBtn" class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">⟲ Undo</button>
                            <button id="redoBtn" class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">⟳ Redo</button>
                            <button id="clearBtn" class="px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">Clear</button>
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
                                    <option value="up">Up</option>
                                    <option value="down">Down</option>
                                    <option value="both" selected>Both</option>
                                </select>
                            </label>
                        </div>
                    </div>
                    <div class="relative w-full bg-black" style="height: 400px;">
                        <div id="videoWrapper" class="w-full h-full relative">
                            <video id="videoElement" class="absolute top-0 left-0 w-full h-full object-contain" style="z-index: 0;" autoplay loop muted playsinline>
                                <!--  Video source can be replaced with your own video URL-->
                                <source src="${window.srcVideo}" type="video/mp4">
                            </video>
                            <canvas id="canvasOverlay" class="absolute top-0 left-0" style="z-index: 10; pointer-events: auto;"></canvas>
                        </div>
                    </div>
                    <div class="mt-4 p-3 border border-gray-200 rounded max-h-40 overflow-y-auto">
                        <h3 class="font-semibold mb-2">List of Drawn Shapes:</h3>
                        <ul id="shapesList" class="list-disc pl-5"></ul>
                    </div>
                    <div class="flex justify-end gap-3 mt-4">
                        <button id="saveBtn" class="px-6 py-2">OK</button>
                        <button id="cancelBtn" class="px-6 py-2">Cancel</button>
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

                    init();
                    renderShapesForRule();
                    document.getElementById('saveBtn').addEventListener('click', () => {
                        saveShapesToServer(instanceId);
                        Swal.close();
                    });
                    document.getElementById('cancelBtn').addEventListener('click', () => {
                        window.shapes = [];
                        Swal.close();
                    });

                }
            });
        },
        loadShapesFromServer: processShapesFromServer,
        saveShapes: saveShapesToServer,
    };
})();

window.DrawingTool = DrawingTool;
