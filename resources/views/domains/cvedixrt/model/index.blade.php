@extends('layouts.in')

@section('title', __('cvedixt-model-index.title'))

@section('body')
    <style>
        /* Kiểu cho cây thư mục */
        .tree-item.selected {
            background-color: #e6f3ff;
            border-left: 4px solid #007bff;
        }

        .tree-item .tree-toggle {
            cursor: pointer;
            width: 20px;
            text-align: center;
        }

        .tree-item .children {
            margin-left: 20px;
        }

        /* Kiểu cho bảng và các cột cố định */
        th.sticky, td.sticky {
            position: sticky;
            background: #fff;
            z-index: 10;
            box-shadow: 1px 0 2px rgba(0, 0, 0, 0.1);
        }

        th.sticky.left-0, td.sticky.left-0 {
            left: 0;
        }

        th.sticky.left-50, td.sticky.left-50 {
            left: 50px;
        }

        th.sticky.right-0, td.sticky.right-0 {
            right: 0;
            box-shadow: -1px 0 2px rgba(0, 0, 0, 0.1);
        }

        /* Kiểu cho container bảng */
        .table-container {
            overflow: auto;
            max-width: 100%;
            max-height: 500px;
            position: relative;
            z-index: 1;
        }

        /* Kiểu cho cột tên */
        .name-column {
            max-width: 300px !important;
            width: 300px !important;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
            text-align: left;
        }

        .name-column:hover {
            cursor: pointer;
        }

        /* Kiểu cho header bảng */
        #model-list-table thead {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #f1f5f9;
        }

        /* Kiểu cho cột hành động và dropdown */
        .action-column {
            width: 60px !important;
            position: relative;
            vertical-align: middle !important;
            padding: 0 !important;
        }

        .action-dropdown {
            position: relative;
            display: inline-flex;
            justify-content: center;
            align-items: center;
        }

        .dropdown-toggle {
            background: none !important;
            border: none !important;
            padding: 4px 8px !important;
            font-size: 18px;
            color: #333 !important;
            min-width: 28px;
            min-height: 28px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .dropdown-toggle:focus {
            outline: none !important;
        }

        .action-dropdown .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            min-width: 120px;
            margin-top: 4px;
            display: none;
            flex-direction: column;
            background: #fff;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
            z-index: 1050;
            opacity: 0;
            transform: translateY(-10px);
            transition: opacity 0.2s ease, transform 0.2s ease;
        }

        .action-dropdown.show .dropdown-menu {
            display: flex;
            opacity: 1;
            transform: translateY(0);
        }

        .action-dropdown .dropdown-item {
            padding: 8px 16px;
            font-size: 14px;
            display: flex;
            align-items: center;
            color: #333;
            text-decoration: none;
        }

        .action-dropdown .dropdown-item:hover {
            background-color: #f8f9fa;
        }

        .action-dropdown .dropdown-item i {
            margin-right: 8px;
        }

        /* Đảm bảo dòng bảng đồng nhất */
        #model-list-table tr {
            height: 40px;
            line-height: 40px;
        }

        #model-list-table td {
            vertical-align: middle;
        }

        /* Kiểu cho modal #create-folder-modal */
        #create-folder-modal .modal-dialog {
            max-width: 400px; /* Giới hạn chiều rộng modal */
            margin: 1.75rem auto; /* Căn giữa */
        }

        #create-folder-modal .modal-content {
            border-radius: 8px; /* Bo góc */
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.2); /* Đổ bóng */
            border: none; /* Loại bỏ viền mặc định */
        }

        #create-folder-modal .modal-header {
            border-bottom: 1px solid #e9ecef;
            padding: 16px 24px;
            background: #f8f9fa; /* Màu nền nhẹ */
            align-items: center;
        }

        #create-folder-modal .modal-title {
            font-size: 18px;
            font-weight: 600;
            color: #333;
        }

        #create-folder-modal .close {
            position: absolute;
            top: 16px;
            right: 24px;
            padding: 0;
            background: none;
            border: none;
            font-size: 24px;
            line-height: 1;
            color: #333;
            opacity: 0.7;
            cursor: pointer;
        }

        #create-folder-modal .close:hover {
            opacity: 1;
        }

        #create-folder-modal .close span {
            display: inline-block;
        }

        #create-folder-modal .modal-body {
            padding: 24px;
        }

        #create-folder-modal .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 8px 12px;
            font-size: 14px;
            transition: border-color 0.2s ease;
        }

        #create-folder-modal .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.1);
        }

        #create-folder-modal .modal-footer {
            border-top: 1px solid #e9ecef;
            padding: 16px 24px;
            justify-content: flex-end;
        }

        #create-folder-modal .btn {
            padding: 8px 16px;
            font-size: 14px;
            border-radius: 4px;
            transition: background-color 0.2s ease;
        }

        #create-folder-modal .btn-secondary {
            background-color: #6c757d;
            border-color: #6c757d;
            color: #fff;
        }

        #create-folder-modal .btn-secondary:hover {
            background-color: #5a6268;
            border-color: #5a6268;
        }

        #create-folder-modal .btn-primary {
            background-color: #007bff;
            border-color: #007bff;
            color: #fff;
        }

        #create-folder-modal .btn-primary:hover {
            background-color: #0056b3;
            border-color: #0056b3;
        }

        /* Hiệu ứng mở modal */
        #create-folder-modal.fade .modal-dialog {
            transform: translateY(-50px);
            transition: transform 0.3s ease, opacity 0.3s ease;
        }

        #create-folder-modal.show .modal-dialog {
            transform: translateY(0);
        }
    </style>

    <div class="intro-y box p-5">
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4 p-4">{{ session('error') }}</div>
        @endif

        <div class="sm:flex sm:space-x-4 items-center mb-4">
            <button class="btn btn-success" onclick="document.getElementById('model-files').click();">
                <i class="fas fa-upload mr-2"></i> Add File
            </button>
            <button class="btn btn-primary" onclick="showCreateFolderModal()">
                <i class="fas fa-folder-plus mr-2"></i> New Folder
            </button>
            <button class="btn btn-danger" id="delete-folder" disabled>
                <i class="fas fa-trash-alt mr-2"></i> Delete Folder
            </button>
            <button class="btn btn-danger" id="delete-selected">
                <i class="fas fa-trash mr-2"></i> Delete Selected Files
            </button>
            <div class="flex-grow"></div>
            <form method="GET" class="flex-grow mt-2 sm:mt-0">
                <input type="search" name="search" class="form-control form-control-lg"
                       placeholder="{{__('cvedixrt-model.filter')}}" data-table-search="#model-list-table"
                       value="{{ request('search') }}"/>
            </form>
        </div>

        <form method="POST" action="{{ route('cvedixrt_model.create') }}" enctype="multipart/form-data"
              id="upload-form" class="hidden">
            @csrf
            <input type="hidden" name="name" value="Uploaded Model {{ now()->format('Y-m-d H:i:s') }}">
            <input type="hidden" name="parent_id" id="upload-parent-id">
            <input type="file" name="model_files[]" id="model-files" class="hidden"
                   accept=".pt,.pth,.pb,.h5,.ckpt,.onnx,.joblib,.pkl,.mp4,.jpg,.jpeg,.png,.avif" multiple required>
        </form>

        <div id="progress-container" class="mt-4 hidden">
            <label>{{ __('Đang tải lên...') }}</label>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div id="progress-bar" class="bg-blue-600 h-4 rounded-full"
                     style="width: 0%; transition: width 0.3s ease;"></div>
            </div>
            <p id="progress-text" class="text-sm text-gray-600 mt-1">0%</p>
        </div>

        <div class="flex mt-5 gap-4">
            <div class="relative overflow-x-auto w-1/3 mt-5" id="model-tree"></div>

            <div class="table-container mt-5 w-2/3">
                <table id="model-list-table"
                       class="table table-report w-full font-medium text-center whitespace-nowrap border border-gray-200"
                       data-table-sort data-table-pagination data-table-pagination-limit="10">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="w-[50px] border border-gray-200 sticky left-0 bg-white z-10"><input type="checkbox"
                                                                                                       id="select-all">
                        </th>
                        <th class="border border-gray-200 sticky left-50 bg-white z-10 name-column">Name</th>
                        <th class="w-[150px] border border-gray-200">Create At</th>
                        <th class="w-[100px] border border-gray-200">Size</th>
                        <th class="w-[150px] border border-gray-200">Type</th>
                        <th class="action-column border border-gray-200 sticky right-0 bg-white z-10">Action</th>
                    </tr>
                    </thead>
                    <tbody id="model-list-body"></tbody>
                </table>
            </div>
        </div>

        <div class="modal fade" id="create-folder-modal" tabindex="-1" aria-labelledby="createFolderModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createFolderModalLabel">Create new folder</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="new-folder-name" class="form-control" placeholder="Folder Name">
                        <input type="hidden" id="new-folder-parent-id" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary"
                                onclick="createNewFolder(document.getElementById('new-folder-parent-id').value)">Create
                        </button>
                    </div>
                </div>
            </div>
        </div>

        @include('molecules.delete-modal', [
            'method' => 'delete',
            'route' => route('cvedixrt_model.delete'),
            'title' => __('Xóa mô hình'),
            'message' => __('Bạn có chắc chắn muốn xóa các mô hình đã chọn?'),
        ])
    </div>
@endsection
@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        let treeData = @json($tree);
        let selectedPath = '';
        let selectedModelId = null;
        let originalData = [];

        function renderTree(tree, parentElement, level = 0, path = '') {
            Object.entries(tree).forEach(([name, node]) => {
                const isFolder = node.is_folder;
                if (!isFolder) return;
                const currentPath = path ? `${path}/${name}` : name;
                const fileCount = node.file_count || 0;
                const itemDiv = document.createElement('div');
                itemDiv.className = `tree-item folder ${selectedPath === currentPath ? 'selected' : ''}`;
                itemDiv.style.marginLeft = `${level * 20}px`;
                itemDiv.setAttribute('data-path', currentPath);
                itemDiv.setAttribute('data-model-id', node.model_id || '');

                const contentDiv = document.createElement('div');
                contentDiv.className = 'flex items-center justify-between p-2 border-b hover:bg-gray-100 transition-colors';

                const span = document.createElement('span');
                span.className = 'flex items-center';

                const toggle = document.createElement('span');
                toggle.className = 'tree-toggle expandable cursor-pointer mr-2';
                toggle.setAttribute('data-path', currentPath);
                toggle.textContent = '▶';
                toggle.addEventListener('click', () => toggleFolder(currentPath, node.model_id));
                span.appendChild(toggle);

                const icon = document.createElement('span');
                icon.className = 'icon';
                icon.textContent = `📂`;
                span.appendChild(icon);

                const link = document.createElement('a');
                link.className = 'ml-2 text-sm font-semibold';
                link.href = 'javascript:;';
                link.title = name;
                link.style.maxWidth = '200px';
                link.style.overflow = 'hidden';
                link.style.textOverflow = 'ellipsis';
                link.style.whiteSpace = 'nowrap';
                link.textContent = `${name} (${fileCount})`;
                link.addEventListener('click', () => toggleFolder(currentPath, node.model_id));
                span.appendChild(link);

                contentDiv.appendChild(span);

                itemDiv.appendChild(contentDiv);

                const childrenDiv = document.createElement('div');
                childrenDiv.className = 'children';
                childrenDiv.style.display = 'none';
                itemDiv.appendChild(childrenDiv);
                if (Object.keys(node.children).length > 0) {
                    renderTree(node.children, childrenDiv, level + 1, currentPath);
                }

                parentElement.appendChild(itemDiv);
            });
        }

        function toggleFolder(path, modelId) {
            const item = document.querySelector(`.tree-item[data-path="${path}"]`);
            if (!item) return;

            const toggle = item.querySelector('.tree-toggle');
            const children = item.querySelector('.children');
            if (!children || !toggle) return;

            const isExpanded = children.style.display === 'block';
            children.style.display = isExpanded ? 'none' : 'block';
            toggle.textContent = isExpanded ? '▶' : '▼';

            document.querySelectorAll('.tree-item').forEach(item => item.classList.remove('selected'));
            item.classList.add('selected');
            selectedPath = path;
            selectedModelId = modelId ? parseInt(modelId) : null;

            const deleteButton = document.getElementById('delete-folder');
            deleteButton.disabled = !selectedModelId;

            if (!isExpanded) {
                fetchFiles(path);
            }
        }

        async function fetchFiles(path) {
            try {
                const cleanPath = path ? path.replace(/^\/|\/$/g, '') : '';
                const encodedPath = encodeURIComponent(cleanPath).replace(/%2F/g, '/');
                const url = `{{ route('cvedixrt_model.folder.contents', ['path' => ':path']) }}`.replace(':path', encodedPath || '');

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }

                const data = await response.json();
                originalData = data.model || [];
                renderTable(originalData);
            } catch (error) {
                showError('Lỗi khi tải danh sách file: ' + error.message);
                document.getElementById('model-list-body').innerHTML = '';
            }
        }

        function renderTable(data) {
            const tableBody = document.getElementById('model-list-body');
            tableBody.innerHTML = data
                .filter(item => !item.is_folder)
                .map(item => `
                    <tr class="hover:bg-gray-50">
                        <td class="w-[50px] border border-gray-200 sticky left-0"><input type="checkbox" class="file-checkbox" data-id="${item.id}"></td>
                        <td class="border border-gray-200 sticky left-50 name-column" title="${item.name}">${item.name}</td>
                        <td class="w-[150px] border border-gray-200">${new Date(item.created_at * 1000).toLocaleDateString('vi-VN')}</td>
                        <td class="w-[100px] border border-gray-200">${(item.size / 1024).toFixed(2)} KB</td>
                        <td class="w-[150px] border border-gray-200">${item.type}</td>
                        <td class="action-column border border-gray-200 right-0">
                            <div class="action-dropdown dropdown d-inline-block" style="position: relative;">
                                    <button class="btn btn-secondary btn-sm dropdown-toggle p-1" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="min-width: 28px; min-height: 28px; line-height: 1;">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <div class="dropdown-menu dropdown-menu-right" style="position: absolute; top: 100%; right: 0;">
                                        <a class="dropdown-item text-success" href="{{ url('cvedixrt-model') }}/${item.id}/download">
                                            <i class="fas fa-download"></i> Download
                                        </a>
                                        <a class="dropdown-item text-danger" href="javascript:;" data-toggle="modal" data-target="#delete-modal"
                                           onclick="document.getElementById('delete-model-id').value='${item.id}';document.getElementById('delete-model-name').innerText='${item.name}';">
                                            <i class="fas fa-trash-alt"></i> Delete
                                        </a>
                                    </div>
                                </div>
                        </td>
                    </tr>
                `).join('');
        }

        async function createNewFolder(parentPath) {
            const folderName = document.getElementById('new-folder-name').value.trim();
            if (!folderName) {
                showError('Tên thư mục không được để trống');
                return;
            }

            if (/[<>:"/\\|?*]/.test(folderName)) {
                showError('Tên thư mục chứa ký tự không hợp lệ');
                return;
            }

            const formData = new FormData();
            formData.append('parent_id', parentPath);
            formData.append('name', folderName);
            formData.append('_token', '{{ csrf_token() }}');

            try {
                const response = await $.ajax({
                    url: '{{ route('cvedixrt_model.create-folder') }}',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    }
                });

                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Thành công',
                        text: response.message || 'Tạo thư mục thành công',
                        timer: 2000,
                        showConfirmButton: false
                    });
                    $('#create-folder-modal').modal('hide');
                    document.getElementById('new-folder-name').value = '';
                    const refreshPath = response.data?.parentPath || parentPath || selectedPath;
                    refreshTree(refreshPath);
                    fetchFiles(refreshPath);
                } else {
                    showError(response.message || 'Không thể tạo thư mục');
                }
            } catch (error) {
                showError('Lỗi khi tạo thư mục: ' + (error.responseJSON?.message || error.message));
            }
        }

        function refreshTree(pathToExpand = '') {
            $.ajax({
                url: '{{ route('cvedixrt_model.index') }}',
                type: 'GET',
                dataType: 'json',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.success && response.data && response.data.tree) {
                        treeData = response.data.tree;
                        const modelTree = document.getElementById('model-tree');
                        modelTree.innerHTML = '';
                        renderTree(treeData, modelTree);
                        toggleFolder(pathToExpand || '', null);
                    } else {
                        showError('Cấu trúc dữ liệu phản hồi không hợp lệ');
                    }
                },
                error: function (xhr) {
                    showError('Lỗi khi làm mới cây thư mục: ' + (xhr.responseJSON?.message || xhr.statusText));
                }
            });
        }

        function showSuccess(message) {
            Swal.fire({
                toast: true,
                icon: 'success',
                title: message,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        }

        function showError(message) {
            Swal.fire({
                toast: true,
                icon: 'error',
                title: message,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        }

        function showCreateFolderModal(parentPath = '') {
            document.getElementById('new-folder-parent-id').value = parentPath || selectedPath;
            document.getElementById('new-folder-name').value = '';
            $('#create-folder-modal').modal('show');
        }

        document.addEventListener('DOMContentLoaded', () => {
            const modelTree = document.getElementById('model-tree');
            renderTree(treeData, modelTree);
            toggleFolder('', null);

            const searchInput = document.querySelector('input[name="search"]');
            searchInput.addEventListener('input', function () {
                const searchValue = this.value.trim().toLowerCase();
                const filteredData = originalData.filter(item =>
                    !item.is_folder && item.name.toLowerCase().includes(searchValue)
                );
                renderTable(filteredData);
            });

            document.getElementById('delete-folder').addEventListener('click', () => {
                if (!selectedModelId) {
                    showError('Vui lòng chọn một thư mục để xóa');
                    return;
                }
                const selectedItem = document.querySelector(`.tree-item[data-model-id="${selectedModelId}"]`);
                const folderName = selectedItem.querySelector('a').textContent.split(' (')[0];
                document.getElementById('delete-model-id').value = selectedModelId;
                document.getElementById('delete-model-name').innerText = folderName;
                $('#delete-modal').modal('show');
            });

            const fileInput = document.getElementById('model-files');
            const uploadForm = document.getElementById('upload-form');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            fileInput.addEventListener('change', function () {
                document.getElementById('upload-parent-id').value = selectedPath;
                if (fileInput.files.length > 0) {
                    progressContainer.classList.remove('hidden');
                    progressBar.style.width = '0%';
                    progressText.textContent = '0%';
                    const formData = new FormData(uploadForm);

                    const xhr = new XMLHttpRequest();
                    xhr.open('POST', uploadForm.action, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.setRequestHeader('X-CSRF-TOKEN', '{{ csrf_token() }}');

                    xhr.upload.addEventListener('progress', function (event) {
                        if (event.lengthComputable) {
                            const percentComplete = Math.round((event.loaded / event.total) * 100);
                            progressBar.style.width = percentComplete + '%';
                            progressText.textContent = percentComplete + '%';
                        }
                    });

                    xhr.addEventListener('load', function () {
                        progressContainer.classList.add('hidden');
                        fileInput.value = '';
                        try {
                            const response = JSON.parse(xhr.responseText);
                            if (xhr.status === 200 && response.success) {
                                showSuccess(response.message || 'Tải lên thành công');
                                setTimeout(() => {
                                    refreshTree(selectedPath);
                                    fetchFiles(selectedPath);
                                }, 1000);
                            } else {
                                showError(response.message || 'Lỗi khi tải lên');
                            }
                        } catch (e) {
                            showError('Lỗi phân tích phản hồi: ' + e.message);
                        }
                    });

                    xhr.addEventListener('error', function () {
                        progressContainer.classList.add('hidden');
                        fileInput.value = '';
                        showError('Lỗi mạng khi tải lên');
                    });

                    xhr.send(formData);
                }
            });

            document.getElementById('delete-selected').addEventListener('click', () => {
                const checkedIds = Array.from(document.querySelectorAll('.file-checkbox:checked'))
                    .map(checkbox => checkbox.getAttribute('data-id'));
                if (checkedIds.length === 0) {
                    showError('Vui lòng chọn ít nhất một file để xóa');
                    return;
                }
                document.getElementById('delete-model-id').value = checkedIds.join(',');
                document.getElementById('delete-model-name').innerText = `(${checkedIds.length} file đã chọn)`;
                $('#delete-modal').modal('show');
            });

            document.getElementById('select-all').addEventListener('change', function () {
                document.querySelectorAll('.file-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });

            // Xử lý sự kiện click vào biểu tượng 3 chấm
            document.getElementById('model-list-table').addEventListener('click', function (e) {
                const toggle = e.target.closest('.dropdown-toggle');
                if (!toggle) return;

                e.stopPropagation();

                const dropdown = toggle.closest('.action-dropdown');
                const isOpen = dropdown.classList.contains('show');

                // Đóng tất cả dropdown khác
                document.querySelectorAll('.action-dropdown').forEach(d => {
                    d.classList.remove('show');
                });

                // Mở/đóng dropdown hiện tại
                if (!isOpen) {
                    dropdown.classList.add('show');
                    $(toggle).dropdown();
                }
            });

            // Đóng tất cả dropdown khi nhấn ra ngoài
            document.addEventListener('click', function (e) {
                if (!e.target.closest('.action-dropdown')) {
                    document.querySelectorAll('.action-dropdown').forEach(d => {
                        d.classList.remove('show');
                    });
                }
            });

            // Xử lý sự kiện khi nhấn nút xác nhận xóa trong modal
            const deleteModal = document.getElementById('delete-modal');
            if (deleteModal) {
                deleteModal.querySelector('.btn-primary').addEventListener('click', async () => {
                    const modelId = document.getElementById('delete-model-id').value;
                    if (!modelId) {
                        showError('ID không hợp lệ');
                        return;
                    }

                    try {
                        const response = await $.ajax({
                            url: '{{ route('cvedixrt_model.delete') }}',
                            type: 'DELETE',
                            data: {
                                model_id: modelId,
                                _token: '{{ csrf_token() }}'
                            },
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest',
                                'Accept': 'application/json'
                            }
                        });

                        if (response.success) {
                            showSuccess(response.message || 'Xóa thành công');
                            $('#delete-modal').modal('hide');
                            refreshTree(selectedPath);
                            fetchFiles(selectedPath);
                        } else {
                            showError(response.message || 'Lỗi khi xóa');
                        }
                    } catch (error) {
                        showError('Lỗi khi xóa: ' + (error.responseJSON?.message || error.message));
                    }
                });
            }
        });
    </script>
@endpush
