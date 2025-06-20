@extends('layouts.in')

@section('title', __('cvedixt-model-index.title'))

@section('body')
    <style>
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

        .alert {
            position: relative;
            z-index: 1000;
        }

        /* Cố định cột trong bảng */
        th.sticky, td.sticky {
            position: sticky;
            background: #fff;
            z-index: 10;
            box-shadow: 1px 0 2px rgba(0, 0, 0, 0.1); /* Bóng nhẹ để phân biệt */
        }

        th.sticky.left-0, td.sticky.left-0 {
            left: 0;
        }

        th.sticky.left-50, td.sticky.left-50 {
            left: 50px; /* Cột Tên ở ngay sau cột Select */
        }

        th.sticky.right-0, td.sticky.right-0 {
            right: 0;
            box-shadow: -1px 0 2px rgba(0, 0, 0, 0.1); /* Bóng trái cho cột Hành động */
        }

        /* Đảm bảo bảng có thể cuộn ngang */
        .table-container {
            overflow-x: auto;
            max-width: 100%;
        }
    </style>

    <div class="intro-y box p-5">
        <!-- Thông báo -->
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4 p-4">{{ session('error') }}</div>
        @endif

        <!-- Thanh công cụ -->
        <div class="sm:flex sm:space-x-4 items-center mb-4">
            <button class="btn btn-success" onclick="document.getElementById('model-files').click();">
                <i class="fas fa-upload mr-2"></i> Add File
            </button>
            <button class="btn btn-primary" onclick="showCreateFolderModal()">
                <i class="fas fa-folder-plus mr-2"></i> New Folder
            </button>
            <button class="btn btn-primary" id="rename-folder" disabled>
                <i class="fas fa-edit mr-2"></i> Rename Folder
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

        <!-- Form upload file -->
        <form method="POST" action="{{ route('cvedixrt_model.create') }}" enctype="multipart/form-data"
              id="upload-form" class="hidden">
            @csrf
            <input type="hidden" name="name" value="Uploaded Model {{ now()->format('Y-m-d H:i:s') }}">
            <input type="hidden" name="parent_id" id="upload-parent-id">
            <input type="file" name="model_files[]" id="model-files" class="hidden"
                   accept=".pt,.pth,.pb,.h5,.ckpt,.onnx,.joblib,.pkl,.mp4,.jpg,.jpeg,.png,.avif" multiple required>
        </form>

        <!-- Thanh tiến trình upload -->
        <div id="progress-container" class="mt-4 hidden">
            <label>{{ __('Đang tải lên...') }}</label>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div id="progress-bar" class="bg-blue-600 h-4 rounded-full"
                     style="width: 0%; transition: width 0.3s ease;"></div>
            </div>
            <p id="progress-text" class="text-sm text-gray-600 mt-1">0%</p>
        </div>

        <!-- Cây thư mục và bảng file -->
        <div class="flex mt-5 gap-4">
            <!-- Cây thư mục -->
            <div class="relative overflow-x-auto w-1/3" id="model-tree"></div>

            <!-- Bảng hiển thị file -->
            <div class="table-container mt-5 w-2/3">
                <table id="model-list-table"
                       class="table table-report w-full font-medium text-center whitespace-nowrap border border-gray-200"
                       data-table-sort data-table-pagination data-table-pagination-limit="10">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="w-[50px] border border-gray-200 sticky left-0 bg-white z-10"><input type="checkbox"
                                                                                                       id="select-all">
                        </th>
                        <th class="w-[200px] border border-gray-200 sticky left-50 bg-white z-10">Tên</th>
                        <th class="w-[150px] border border-gray-200">Ngày tạo</th>
                        <th class="w-[100px] border border-gray-200">Kích thước</th>
                        <th class="w-[150px] border border-gray-200">Loại</th>
                        <th class="w-[200px] border border-gray-200 sticky right-0 bg-white z-10">Hành động</th>
                    </tr>
                    </thead>
                    <tbody id="model-list-body"></tbody>
                </table>
            </div>
        </div>

        <!-- Modal tạo thư mục -->
        <div class="modal fade" id="create-folder-modal" tabindex="-1" aria-labelledby="createFolderModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="createFolderModalLabel">Tạo thư mục mới</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="text" id="new-folder-name" class="form-control" placeholder="Nhập tên thư mục">
                        <input type="hidden" id="new-folder-parent-id" value="">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                        <button type="button" class="btn btn-primary"
                                onclick="createNewFolder(document.getElementById('new-folder-parent-id').value)">Tạo
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal xóa -->
        @include('molecules.delete-modal', [
            'method' => 'delete',
            'route' => route('cvedixrt_model.delete', 0),
            'title' => __('Xóa mô hình'),
            'message' => __('Bạn có chắc chắn muốn xóa các mô hình đã chọn?'),
        ])

        <!-- Modal đổi tên -->
        @include('molecules.rename-modal', [
            'method' => 'put',
            'route' => route('cvedixrt_model.rename'),
            'title' => __('Đổi tên mô hình'),
            'message' => __('Nhập tên mới cho mô hình'),
        ])
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <script>
        let treeData = @json($tree);
        let selectedPath = ''; // Biến lưu đường dẫn thư mục đang chọn
        let selectedModelId = null; // Biến lưu model_id của thư mục đang chọn

        // Hàm render cây thư mục
        function renderTree(tree, parentElement, level = 0, path = '') {
            Object.entries(tree).forEach(([name, node]) => {
                const isFolder = node.is_folder;
                if (!isFolder) return; // Chỉ hiển thị thư mục
                const currentPath = path ? `${path}/${name}` : name;
                const fileCount = node.file_count || 0;
                const itemDiv = document.createElement('div');
                itemDiv.className = `tree-item folder ${node.deleted_at ? 'line-through' : ''} ${selectedPath === currentPath ? 'selected' : ''}`;
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

        // Hàm toggle thư mục
        function toggleFolder(path, modelId) {
            const item = document.querySelector(`.tree-item[data-path="${path}"]`);
            if (!item) return;

            const toggle = item.querySelector('.tree-toggle');
            const children = item.querySelector('.children');
            if (!children || !toggle) return;

            const isExpanded = children.style.display === 'block';
            children.style.display = isExpanded ? 'none' : 'block';
            toggle.textContent = isExpanded ? '▶' : '▼';

            // Cập nhật thư mục được chọn
            document.querySelectorAll('.tree-item').forEach(item => item.classList.remove('selected'));
            item.classList.add('selected');
            selectedPath = path;
            selectedModelId = modelId;

            // Kích hoạt hoặc vô hiệu hóa nút Sửa/Xóa thư mục
            const renameButton = document.getElementById('rename-folder');
            const deleteButton = document.getElementById('delete-folder');
            if (selectedModelId) {
                renameButton.disabled = false;
                deleteButton.disabled = false;
            } else {
                renameButton.disabled = true;
                deleteButton.disabled = true;
            }

            if (!isExpanded) {
                fetchFiles(path);
            }
        }

        // Hàm lấy danh sách file
        async function fetchFiles(path) {
            try {
                const cleanPath = path.replace(/^\//, '').replace(/\/$/, '');
                const encodedPath = encodeURIComponent(cleanPath).replace(/%2F/g, '/'); // Giữ dấu / trong subfolder
                const url = `{{ route('cvedixrt_model.folder.contents', ['path' => ':path']) }}`.replace(':path', encodedPath);
                console.log('Fetching files from:', url);

                const response = await fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                if (!response.ok) {
                    const errorText = await response.text();
                    throw new Error(`HTTP error! Status: ${response.status}, Response: ${errorText.substring(0, 100)}...`);
                }

                const data = await response.json();
                if (!data.model) {
                    throw new Error(data.message || 'Lỗi server');
                }

                const tableBody = document.getElementById('model-list-body');
                tableBody.innerHTML = (data.model || [])
                    .filter(item => !item.is_folder) // Chỉ hiển thị file
                    .map(item => `
                        <tr class="hover:bg-gray-50 ${item.deleted_at ? 'line-through' : ''}">
                            <td class="w-[50px] border border-gray-200 sticky left-0"><input type="checkbox" class="file-checkbox" data-id="${item.id}"></td>
                            <td class="w-[200px] border border-gray-200 sticky left-50">${item.name}</td>
                            <td class="w-[150px] border border-gray-200">${new Date(item.created_at * 1000).toLocaleDateString('vi-VN')}</td>
                            <td class="w-[100px] border border-gray-200">${(item.size / 1024).toFixed(2)} KB</td>
                            <td class="w-[150px] border border-gray-200">${item.type}</td>
                            <td class="w-[200px] border border-gray-200 sticky right-0">
    <a href="{{ url('cvedixrt_model/download') }}/${item.id}" class="btn btn-success btn-sm mr-2" title="Tải xuống">
        <i class="fas fa-download"></i>
    </a>
    <a href="javascript:;" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#rename-modal"
       onclick="document.getElementById('rename-model-id').value='${item.id}';document.getElementById('rename-model-name').value='${item.name}';" title="Sửa">
        <i class="fas fa-edit"></i>
    </a>
    <a href="javascript:;" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-modal"
       onclick="document.getElementById('delete-model-id').value='${item.id}';document.getElementById('delete-model-name').innerText='${item.name}';" title="Xóa">
        <i class="fas fa-trash-alt"></i>
    </a>
</td>
                        </tr>
                    `).join('');
            } catch (error) {
                showError('Lỗi khi tải danh sách file: ' + error.message);
                console.error('Fetch error:', error);
                document.getElementById('model-list-body').innerHTML = '';
            }
        }

        // Hàm tạo thư mục mới
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
                    showSuccess(response.message || 'Tạo thư mục thành công');
                    $('#create-folder-modal').modal('hide');
                    document.getElementById('new-folder-name').value = '';
                    refreshTree(parentPath);
                    fetchFiles(parentPath);
                } else {
                    showError(response.message || 'Không thể tạo thư mục');
                }
            } catch (error) {
                showError('Lỗi khi tạo thư mục: ' + (error.responseJSON?.message || error.message));
                console.error('Create folder error:', error);
            }
        }

        // Hàm làm mới cây
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
                    treeData = response.data.tree;
                    const modelTree = document.getElementById('model-tree');
                    modelTree.innerHTML = '';
                    renderTree(treeData, modelTree);
                    toggleFolder(pathToExpand || '', null);
                },
                error: function (xhr) {
                    showError('Lỗi khi làm mới cây thư mục: ' + (xhr.responseJSON?.message || xhr.statusText));
                }
            });
        }

        // Hàm hiển thị thông báo thành công
        function showSuccess(message) {
            const successDiv = document.createElement('div');
            successDiv.className = 'alert alert-success mb-4 p-4';
            successDiv.style.display = 'block';
            successDiv.textContent = message;
            document.querySelector('.intro-y .box').insertBefore(successDiv, document.querySelector('.intro-y .box').firstChild);
            setTimeout(() => successDiv.remove(), 2000);
        }

        // Hàm hiển thị thông báo lỗi
        function showError(message) {
            const errorDiv = document.createElement('div');
            errorDiv.className = 'alert alert-danger mb-4 p-4';
            errorDiv.style.display = 'block';
            errorDiv.textContent = 'Lỗi: ' + message;
            const box = document.querySelector('.intro-y .box');
            if (box) {
                box.insertBefore(errorDiv, box.firstChild);
            }
            setTimeout(() => errorDiv.remove(), 2000);
        }

        // Hàm hiển thị modal tạo thư mục
        function showCreateFolderModal(parentPath = '') {
            document.getElementById('new-folder-parent-id').value = parentPath || selectedPath;
            document.getElementById('new-folder-name').value = '';
            $('#create-folder-modal').modal('show');
        }

        // Khởi tạo khi trang được tải
        document.addEventListener('DOMContentLoaded', () => {
            const modelTree = document.getElementById('model-tree');
            renderTree(treeData, modelTree);
            toggleFolder('', null); // Mở thư mục root mặc định

            // Xử lý nút Sửa thư mục
            document.getElementById('rename-folder').addEventListener('click', () => {
                if (!selectedModelId) {
                    showError('Vui lòng chọn một thư mục để sửa');
                    return;
                }
                const selectedItem = document.querySelector(`.tree-item[data-model-id="${selectedModelId}"]`);
                const folderName = selectedItem.querySelector('a').textContent.split(' (')[0]; // Loại bỏ số file count
                document.getElementById('rename-model-id').value = selectedModelId;
                document.getElementById('rename-model-name').value = folderName;
                $('#rename-modal').modal('show');
            });

            // Xử lý nút Xóa thư mục
            document.getElementById('delete-folder').addEventListener('click', () => {
                if (!selectedModelId) {
                    showError('Vui lòng chọn một thư mục để xóa');
                    return;
                }
                const selectedItem = document.querySelector(`.tree-item[data-model-id="${selectedModelId}"]`);
                const folderName = selectedItem.querySelector('a').textContent.split(' (')[0]; // Loại bỏ số file count
                document.getElementById('delete-model-id').value = selectedModelId;
                document.getElementById('delete-model-name').innerText = folderName;
                $('#delete-modal').modal('show');
            });

            // Xử lý upload file
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
                                setTimeout(() => refreshTree(selectedPath), 1000);
                                fetchFiles(selectedPath);
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

            // Xử lý xóa nhiều file
            document.getElementById('delete-selected').addEventListener('click', () => {
                const checkedIds = Array.from(document.querySelectorAll('.file-checkbox:checked'))
                    .map(checkbox => checkbox.getAttribute('data-id'));
                if (checkedIds.length > 0) {
                    document.getElementById('delete-model-id').value = checkedIds.join(',');
                    document.getElementById('delete-model-name').innerText = `(${checkedIds.length} file đã chọn)`;
                    $('#delete-modal').modal('show');
                } else {
                    showError('Vui lòng chọn ít nhất một file để xóa');
                }
            });

            // Chọn tất cả
            document.getElementById('select-all').addEventListener('change', function () {
                document.querySelectorAll('.file-checkbox').forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
            });
        });
    </script>
@endpush
