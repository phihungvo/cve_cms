@extends('layouts.in')

@section('title', __('cvedixt-model-index.title'))

@section('body')
    <div class="intro-y box p-5">
        @if(session('success'))
            <div class="alert alert-success mb-4 p-4">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger mb-4 p-4">{{ session('error') }}</div>
        @endif

        <!-- Search Form and Upload/New Folder Buttons -->
        <div class="sm:flex sm:space-x-4">
            <form method="get" class="flex-grow mt-2 sm:mt-0">
                <input type="search" name="search" class="form-control form-control-lg"
                       placeholder="{{__('cvedixrt-model.filter')}}" data-table-search="#model-list-table"
                       value="{{ request('search') }}"/>
            </form>
            <form method="POST" action="{{ route('cvedixrt_model.create') }}" enctype="multipart/form-data"
                  id="upload-form" class="sm:ml-4 mt-2 sm:mt-0">
                @csrf
                <input type="hidden" name="name" value="Uploaded Model {{ now()->format('Y-m-d H:i:s') }}">
                <div class="flex items-center mb-2">
                    <select name="bucket_path" id="folder-select" class="form-control form-control-lg mr-2" required>
                        <option value="">Chọn thư mục</option>
                        @foreach ($tree as $rootName => $children)
                            <option value="{{ $rootName }}">{{ $rootName }}</option>
                            @if (is_array($children))
                                @foreach (array_keys($children) as $subFolder)
                                    <option value="{{ $rootName }}/{{ $subFolder }}">{{ $rootName }}
                                        /{{ $subFolder }}</option>
                                @endforeach
                            @endif
                        @endforeach
                    </select>
                    <input type="file" name="model_files[]" id="model-files" class="hidden" accept="video/mp4" multiple
                           required>
                    <button type="button" class="btn btn-success form-control-lg whitespace-nowrap"
                            onclick="document.getElementById('model-files').click();">
                        {{ __('cvedixrt-model.upload') }}
                    </button>
                </div>
            </form>
            {{-- New Folder Form --}}
            <form method="POST" action="{{ route('cvedixrt_model.create-folder') }}" id="new-folder-form"
                  class="mt-2 sm:mt-0">
                @csrf
                <input type="hidden" name="parent_id" id="new-folder-parent-id">
                <input type="text" name="name" class="form-control form-control-lg hidden" id="new-folder-name"
                       placeholder="Tên thư mục">
                <button type="button" class="btn btn-primary form-control-lg whitespace-nowrap"
                        onclick="document.getElementById('new-folder-name').value = 'Folder_' + new Date().toLocaleTimeString('vi-VN',
                         {hour12: false}); document.getElementById('new-folder-form').submit();">
                    {{ __('Tạo thư mục mới') }}
                </button>
            </form>
        </div>

        <!-- Progress Bar -->
        <div id="progress-container" class="mt-4 hidden">
            <label>{{ __('Đang tải lên...') }}</label>
            <div class="w-full bg-gray-200 rounded-full h-4">
                <div id="progress-bar" class="bg-blue-600 h-4 rounded-full"
                     style="width: 0%; transition: width 0.3s ease;"></div>
            </div>
            <p id="progress-text" class="text-sm text-gray-600 mt-1">0%</p>
        </div>

        <!-- Tree View -->
        <div class="flex mt-5 gap-4">
            <div class="relative overflow-x-auto w-1/3" id="model-tree">
                <!-- Cây thư mục sẽ được render bằng JavaScript -->
            </div>

            <!-- Table to show files -->
            <div class="relative overflow-x-auto mt-5 w-2/3">
                <table id="model-list-table"
                       class="table table-report w-full font-medium text-center whitespace-nowrap border border-gray-200"
                       data-table-sort data-table-pagination data-table-pagination-limit="10">
                    <thead class="bg-gray-100">
                    <tr>
                        <th class="w-[200px] fixed-left border border-gray-200">{{ __('Tên mô hình') }}</th>
                        <th class="w-[300px] border border-gray-200">{{ __('File mô hình') }}</th>
                        <th class="w-[100px] border border-gray-200">{{ __('Kích thước') }}</th>
                        <th class="w-[100px] border border-gray-200">{{ __('Loại') }}</th>
                        <th class="w-[150px] border border-gray-200">{{ __('Tạo lúc') }}</th>
                        <th class="w-[150px] border border-gray-200">{{ __('Cập nhật lúc') }}</th>
                        <th class="w-[100px] border border-gray-200">{{ __('Trạng thái') }}</th>
                        <th class="w-[200px] fixed-right border border-gray-200">{{ __('Hành động') }}</th>
                    </tr>
                    </thead>
                    <tbody id="model-list-body">
                    <!-- Nội dung sẽ được cập nhật bằng JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Delete Modal -->
        @include('molecules.delete-modal', [
            'method' => 'delete',
            'route' => route('cvedixrt_model.delete', 0),
            'title' => __('Xóa mô hình'),
            'message' => __('Bạn có chắc chắn muốn xóa mô hình này?'),
        ])

        <!-- Rename Modal -->
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
    <script>
        let treeData = @json($tree);

        function renderTree(tree, parentElement, level = 0, path = '') {
            Object.entries(tree).forEach(([name, children]) => {
                const isFolder = children && typeof children === 'object' && Object.keys(children).length > 0;
                const currentPath = path ? `${path}/${name}` : name;
                const itemDiv = document.createElement('div');
                itemDiv.className = `tree-item ${isFolder ? 'folder' : 'file'} ${path.includes('deleted') ? 'line-through' : ''}`;
                itemDiv.style.marginLeft = `${level * 20}px`;
                itemDiv.setAttribute('data-path', currentPath);

                const contentDiv = document.createElement('div');
                contentDiv.className = 'flex items-center justify-between p-2 border-b hover:bg-gray-100 transition-colors';

                const span = document.createElement('span');
                span.className = 'flex items-center';

                if (isFolder) {
                    const toggle = document.createElement('span');
                    toggle.className = 'tree-toggle expandable';
                    toggle.setAttribute('data-path', currentPath);
                    toggle.textContent = '▶';
                    span.appendChild(toggle);
                } else {
                    const spacer = document.createElement('span');
                    spacer.className = 'ml-5';
                    span.appendChild(spacer);
                }

                const icon = document.createElement('span');
                icon.className = 'icon';
                icon.textContent = isFolder ? '📂' : (name.endsWith('.jpg') ? '🖼️' : '📹');
                span.appendChild(icon);

                const link = document.createElement('a');
                link.className = `ml-2 text-sm ${isFolder ? 'font-semibold' : 'text-blue-600 hover:underline'}`;
                link.href = isFolder ? 'javascript:;' : `{{ url('cvedixrt_model/download') }}/${encodeURIComponent(currentPath)}`;
                link.title = name;
                link.style.maxWidth = '200px';
                link.style.overflow = 'hidden';
                link.style.textOverflow = 'ellipsis';
                link.style.whiteSpace = 'nowrap';
                link.textContent = name;
                span.appendChild(link);

                contentDiv.appendChild(span);

                // const sizeSpan = document.createElement('span');
                // sizeSpan.className = 'text-sm text-gray-600';
                // sizeSpan.textContent = isFolder ? '-' : '0.00 MB'; // Kích thước giả lập
                // contentDiv.appendChild(sizeSpan);
                //
                // const dateSpan = document.createElement('span');
                // dateSpan.className = 'text-sm text-gray-600';
                // dateSpan.textContent = new Date().toLocaleString('vi-VN', { timeZone: 'Asia/Ho_Chi_Minh' });
                // contentDiv.appendChild(dateSpan);

                const actionsSpan = document.createElement('span');
                actionsSpan.className = 'actions';
                {{--if (isFolder) {--}}
                {{--    const newFolderLink = document.createElement('a');--}}
                {{--    newFolderLink.href = 'javascript:;';--}}
                {{--    newFolderLink.className = 'btn btn-primary btn-sm mr-2';--}}
                {{--    newFolderLink.textContent = 'Tạo thư mục';--}}
                {{--    newFolderLink.onclick = () => {--}}
                {{--        const formData = new FormData();--}}
                {{--        formData.append('parent_id', currentPath);--}}
                {{--        formData.append('name', 'NewFolder_' + Math.random().toString(36).substr(2, 5));--}}

                {{--        $.ajax({--}}
                {{--            url: '{{ route('cvedixrt_model.create-folder') }}',--}}
                {{--            type: 'POST',--}}
                {{--            data: formData,--}}
                {{--            processData: false,--}}
                {{--            contentType: false,--}}
                {{--            success: function(response) {--}}
                {{--                if (response.success) {--}}
                {{--                    showSuccess(response.message);--}}
                {{--                    // Cập nhật cây thư mục động--}}
                {{--                    const parentItem = document.querySelector(`.tree-item[data-path="${currentPath}"]`);--}}
                {{--                    if (parentItem) {--}}
                {{--                        const childrenDiv = parentItem.querySelector('.children');--}}
                {{--                        if (childrenDiv) {--}}
                {{--                            const itemDiv = document.createElement('div');--}}
                {{--                            itemDiv.className = 'tree-item folder';--}}
                {{--                            itemDiv.style.marginLeft = `${(level + 1) * 20}px`;--}}
                {{--                            itemDiv.setAttribute('data-path', response.path);--}}

                {{--                            const newContentDiv = document.createElement('div');--}}
                {{--                            newContentDiv.className = 'flex items-center justify-between p-2 border-b hover:bg-gray-100 transition-colors';--}}
                {{--                            newContentDiv.innerHTML = `--}}
                {{--                                <span class="flex items-center">--}}
                {{--                                    <span class="tree-toggle expandable" data-path="${response.path}">▶</span>--}}
                {{--                                    <span class="icon">📂</span>--}}
                {{--                                    <a href="javascript:;" class="ml-2 text-sm font-semibold" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${response.name}">${response.name}</a>--}}
                {{--                                </span>--}}
                {{--                                <span class="text-sm text-gray-600">-</span>--}}
                {{--                                <span class="text-sm text-gray-600">${new Date().toLocaleString('vi-VN', { timeZone: 'Asia/Ho_Chi_Minh' })}</span>--}}
                {{--                                <span class="actions">--}}
                {{--                                    <a href="javascript:;" class="btn btn-primary btn-sm mr-2" onclick="createNewFolder('${response.path}')">Tạo thư mục</a>--}}
                {{--                                </span>--}}
                {{--                            `;--}}
                {{--                            itemDiv.appendChild(newContentDiv);--}}
                {{--                            const newChildrenDiv = document.createElement('div');--}}
                {{--                            newChildrenDiv.className = 'children';--}}
                {{--                            newChildrenDiv.style.display = 'none';--}}
                {{--                            itemDiv.appendChild(newChildrenDiv);--}}
                {{--                            childrenDiv.appendChild(itemDiv);--}}
                {{--                            childrenDiv.style.display = 'block'; // Mở thư mục cha--}}
                {{--                            parentItem.querySelector('.tree-toggle').textContent = '▼';--}}
                {{--                        }--}}
                {{--                    }--}}
                {{--                } else {--}}
                {{--                    showError(response.message);--}}
                {{--                }--}}
                {{--            },--}}
                {{--            error: function(xhr, status, error) {--}}
                {{--                showError('Lỗi khi tạo thư mục: ' + error);--}}
                {{--            }--}}
                {{--        });--}}
                {{--    };--}}
                {{--    actionsSpan.appendChild(newFolderLink);--}}
                {{--}--}}
                contentDiv.appendChild(actionsSpan);

                itemDiv.appendChild(contentDiv);

                if (isFolder) {
                    const childrenDiv = document.createElement('div');
                    childrenDiv.className = 'children';
                    childrenDiv.style.display = 'none';
                    itemDiv.appendChild(childrenDiv);
                    renderTree(children, childrenDiv, level + 1, currentPath);
                }

                parentElement.appendChild(itemDiv);
            });
        }

        function createNewFolder(parentPath) {
            const folderName = 'NewFolder_' + Math.random().toString(36).substr(2, 5);
            const formData = new FormData();
            formData.append('parent_id', parentPath);
            formData.append('name', folderName);

            $.ajax({
                url: '{{ route('cvedixrt_model.create-folder') }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        showSuccess(response.message);
                        // Cập nhật cây thư mục
                        const itemDiv = document.querySelector(`.tree-item[data-path="${parentPath}"] .children`);
                        if (itemDiv) {
                            const newItemDiv = document.createElement('div');
                            newItemDiv.className = 'tree-item folder';
                            newItemDiv.style.marginLeft = `${(parentPath.split('/').length + 1) * 20}px`;
                            newItemDiv.setAttribute('data-path', response.path);
                            newItemDiv.innerHTML = `
                                <div class="flex items-center justify-between p-2 border-b hover:bg-gray-100 transition-colors">
                                    <span class="flex items-center">
                                        <span class="tree-toggle expandable" data-path="${response.path}">▶</span>
                                        <span class="icon">📂</span>
                                        <a href="javascript:;" class="ml-2 text-sm font-semibold" style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="${folderName}">${folderName}</a>
                                    </span>
                                    <span class="text-sm text-gray-600">-</span>
                                    <span class="text-sm text-gray-600">${new Date().toLocaleString('vi-VN', {timeZone: 'Asia/Ho_Chi_Minh'})}</span>
                                    <span class="actions">
                                        <a href="javascript:;" class="btn btn-primary btn-sm mr-2" onclick="createNewFolder('${response.path}')">Tạo thư mục</a>
                                    </span>
                                </div>
                                <div class="children" style="display: none;"></div>
                            `;
                            itemDiv.appendChild(newItemDiv);
                            itemDiv.style.display = 'block'; // Mở thư mục cha
                            document.querySelector(`.tree-item[data-path="${parentPath}"] .tree-toggle`).textContent = '▼';
                        }
                    } else {
                        showError(response.message);
                    }
                },
                error: function (xhr, status, error) {
                    showError('Lỗi khi tạo thư mục: ' + error);
                }
            });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const modelTree = document.getElementById('model-tree');
            renderTree(treeData, modelTree);

            document.querySelectorAll('.tree-toggle.expandable').forEach(toggle => {
                toggle.addEventListener('click', function () {
                    const path = this.getAttribute('data-path');
                    const item = document.querySelector(`.tree-item[data-path="${path}"]`);
                    const children = item.querySelector('.children');
                    const isExpanded = children.style.display === 'block';

                    children.style.display = isExpanded ? 'none' : 'block';
                    this.textContent = isExpanded ? '▶' : '▼';

                    if (!isExpanded) {
                        fetchFiles(path).then(data => {
                            const tableBody = document.getElementById('model-list-body');
                            tableBody.innerHTML = data.map(item => `
                                <tr class="hover:bg-gray-50 ${item.deleted_at ? 'line-through' : ''}">
                                    <td class="fixed-left border border-gray-200">${item.name}</td>
                                    <td class="border border-gray-200">${item.model_url}</td>
                                    <td class="border border-gray-200">${(item.size / (1024 * 1024)).toFixed(2)} MB</td>
                                    <td class="border border-gray-200">${item.type}</td>
                                    <td class="border border-gray-200">${new Date(item.created_at * 1000).toLocaleString('vi-VN')}</td>
                                    <td class="border border-gray-200">${item.updated_at ? new Date(item.updated_at * 1000).toLocaleString('vi-VN') : 'N/A'}</td>
                                    <td class="border border-gray-200">${item.deleted_at ? 'Đã xóa' : 'Hoạt động'}</td>
                                    <td class="fixed-right border border-gray-200">
                                        <a href="javascript:;" class="btn btn-primary btn-sm mr-2" data-toggle="modal" data-target="#rename-modal"
                                           onclick="document.getElementById('rename-model-id').value='${item.id}';document.getElementById('rename-model-name').value='${item.name}';">Đổi tên</a>
                                        <a href="javascript:;" class="btn btn-danger btn-sm" data-toggle="modal" data-target="#delete-modal"
                                           onclick="document.getElementById('delete-model-id').value='${item.model_url}';document.getElementById('delete-model-name').innerText='${item.name}';">Xóa</a>
                                    </td>
                                </tr>
                            `).join('');
                        });
                    }
                });
            });

            async function fetchFiles(path) {
                const response = await fetch(`{{ route('cvedixrt_model.folder.contents', ['path' => ':path']) }}`.replace(':path', encodeURIComponent(path)));
                const data = await response.json();
                return data.model || [];
            }

            // Xử lý upload file (giữ nguyên logic cũ)
            const fileInput = document.getElementById('model-files');
            const uploadForm = document.getElementById('upload-form');
            const progressContainer = document.getElementById('progress-container');
            const progressBar = document.getElementById('progress-bar');
            const progressText = document.getElementById('progress-text');

            fileInput.addEventListener('change', function () {
                if (fileInput.files.length > 0) {
                    const uploadButton = uploadForm.querySelector('button');
                    uploadButton.disabled = true;
                    progressContainer.classList.remove('hidden');
                    progressBar.style.width = '0%';
                    progressText.textContent = '0%';
                    const formData = new FormData(uploadForm);
                    const xhr = new XMLHttpRequest();

                    xhr.upload.addEventListener('progress', function (event) {
                        if (event.lengthComputable) {
                            const percentComplete = Math.round((event.loaded / event.total) * 100);
                            progressBar.style.width = percentComplete + '%';
                            progressText.textContent = percentComplete + '%';
                        }
                    });

                    xhr.addEventListener('load', function () {
                        progressContainer.classList.add('hidden');
                        uploadButton.disabled = false;
                        fileInput.value = '';
                        const response = JSON.parse(xhr.responseText);
                        if (xhr.status === 200 && response.success) {
                            showSuccess(response.message || 'Tải lên thành công');
                            setTimeout(() => window.location.reload(), 2000);
                        } else {
                            showError(response.message || 'Lỗi server khi tải lên');
                        }
                    });

                    xhr.addEventListener('error', function () {
                        progressContainer.classList.add('hidden');
                        uploadButton.disabled = false;
                        fileInput.value = '';
                        showError('Lỗi mạng khi tải lên');
                    });

                    xhr.open('POST', uploadForm.action, true);
                    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                    xhr.setRequestHeader('Accept', 'application/json');
                    xhr.send(formData);
                }
            });

            function showSuccess(message) {
                const successDiv = document.createElement('div');
                successDiv.className = 'alert alert-success mb-4 p-4';
                successDiv.style.display = 'block';
                successDiv.style.zIndex = '1000';
                successDiv.style.position = 'relative';
                successDiv.textContent = message;
                document.querySelector('.intro-y.box .p-5').insertBefore(successDiv, document.querySelector('.intro-y.box .p-5').firstChild);
                setTimeout(() => successDiv.remove(), 5000);
            }

            function showError(message) {
                const errorDiv = document.createElement('div');
                errorDiv.className = 'alert alert-error mb-4 p-4';
                errorDiv.style.display = 'block';
                errorDiv.style.zIndex = '1000';
                errorDiv.style.position = 'relative';
                errorDiv.textContent = 'Lỗi: ' + message;
                document.querySelector('.intro-y.box .p-5').insertBefore(errorDiv, document.querySelector('.intro-y.box .p-5').firstChild);
                setTimeout(() => errorDiv.remove(), 10000);
            }
        });
    </script>
@endpush
