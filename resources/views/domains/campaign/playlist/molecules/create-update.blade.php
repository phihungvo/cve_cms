<div class="box p-5 mt-5" {{auth()->user()->isRoleRoot() ? '' : 'style=display:none'}}>
    @if(auth()->user()->isRoleRoot())
        <!-- Enterprise -->
       <div class="p-2" >
            <label for="enterprise_select" class="form-label">{{ __('playlist-create.enterprise') }}</label>
            <div class="input-group">
                <select name="enterprise_id" id="enterprise-select"
                        class="form-control form-control-lg" {{ isset($isUpdate) ? 'disabled' : '' }}>>
                    <option value="">{{ __('playlist-create.select-enterprise') }}</option>
                    @foreach ($enterprises as $enterprise)
                        <option value="{{ $enterprise->id }}"
                            {{ $REQUEST->input('enterprise_id') == $enterprise->id ? 'selected' : '' }}>
                            {{ $enterprise->name }}
                        </option>
                    @endforeach
                </select>
            </div>
       </div>
       @endif
    @if(isset($isUpdate))
                <input type="hidden" name="enterprise_id" value="{{ $REQUEST->input('enterprise_id') }}">
    @endif
</div>
<div class="box p-5 mt-5">
        <!-- name -->
        <div class="p-2">
            <label for="playlist-name" class="form-label">{{ __('playlist-create.name') }}</label>
            <div class="input-group">
                <input type="text" name="name" id="playlist-name" class="form-control form-control-lg"
                       value="{{ old('name', $REQUEST->input('name') ?? '') }}" required>
            </div>
        </div>

        <!-- description -->
        <div class="p-2">
            <label for="playlist-description" class="form-label">{{ __('playlist-create.description') }}</label>
            <div class="input-group">
                <input type="text" name="description" id="playlist-description" class="form-control form-control-lg"
                       value="{{ old('description', $REQUEST->input('description') ?? '') }}">
            </div>
        </div>
        </div>

<div class="box p-5 mt-5">
    <p class="font-bold mb-4">Select medias</p>
    <div class="grid grid-cols-1 gap-4">
        <div id="media-container">
            @php
                $selectedMedias = $selectedMedias ?? [];
            @endphp

                <!-- Hiển thị các media đã chọn (nếu có) -->
            @if (!empty($selectedMedias))
                @foreach ($selectedMedias as $index => $selectedMedia)
                    <div class="media-select-group">
                        <label class="block mt-2">Media #{{ $index + 1 }}</label>
                        <div style="display: flex; align-items: center; gap: 10px" class="mt-2">
                            <video controls
                                   style="width: 80px; height: 120px;">
                                <source src="{{$selectedMedia->media_url}}" type="{{$selectedMedia->type}}">
                                Your browser does not support the video tag.
                            </video>
                            <select class="form-control media-select h-9" name="medias[{{ $index }}][id]">
                                <option value="">{{__('playlist-create.select-media')}}</option>
                                @foreach ($medias->filter(fn($media) => $media->enterprise_id == ($REQUEST->input('enterprise_id') ?? $enterprise->id)) as $media)
                                    <option value="{{ $media->id }}"
                                            data-url="{{ $media->media_url }}"
                                            data-type="{{ $media->type }}"
                                        {{ $media->id == $selectedMedia['id'] ? 'selected' : '' }}>
                                        {{ $media->name }} ({{ $media->duration }}s)
                                    </option>
                                @endforeach
                            </select>
                            <!-- Thêm nút xóa cho media đã chọn -->
                            <button type="button" class="btn btn-danger btn-remove" style="padding: 8px 12px;">✖
                            </button>

                        </div>
                        <input type="hidden" name="medias[{{ $index }}][position]" value="{{ $index + 1 }}"
                               class="media-position">
                    </div>
                @endforeach
            @endif
            <!-- Select box sẽ được thêm bởi JavaScript -->
        </div>
    </div>
</div>

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const mediaContainer = document.getElementById("media-container");
            let selectedMedias = [];

            // Tạo select box
            function createSelectBox(position, isDefault = false) {
                const newSelectGroup = document.createElement("div");
                newSelectGroup.classList.add("media-select-group");
                if (isDefault) {
                    newSelectGroup.classList.add("default-select");
                }

                newSelectGroup.innerHTML = `
                    <label class="block mt-2 mb-2">Media #${position}</label>
                    <div style="display: flex; align-items: center; gap: 10px">

                        <select class="form-control media-select h-9" name="medias[${position - 1}][id]">
                            <option value="">{{__('playlist-create.select-media')}}</option>
                            @foreach ($medias->filter(fn($media) => $media->enterprise_id == ($REQUEST->input('enterprise_id') ?? auth()->user()->enterprise_id)) as $media)
                <option value="{{ $media->id }}"
                                        data-url="{{ $media->media_url }}"
                                        data-type="{{ $media->type }}">
                                    {{ $media->name }} ({{ $media->duration }}s)
                                </option>
                            @endforeach
                </select>

${!isDefault ? '<button type="button" class="btn btn-danger btn-remove" style="padding: 5px 10px;">✖</button>' : ''}
                    </div>
                    <video class="media-preview" style="display: none; max-width: 120px; margin-top: 10px;" controls></video>
                    <input type="hidden" name="medias[${position - 1}][position]" value="${position}" class="media-position">
                `;
                mediaContainer.appendChild(newSelectGroup);
                updateSelectOptions();
                return newSelectGroup;
            }

            // Cập nhật các tùy chọn của select
            function updateSelectOptions() {
                document.querySelectorAll(".media-select").forEach(select => {
                    const currentValue = select.value;
                    const isDefault = select.closest(".media-select-group").classList.contains("default-select");
                    let optionsHtml = '<option value="">{{__('playlist-create.select-media')}}</option>';

                    if (isDefault) {
                        medias.filter(media => media.enterprise_id == document.getElementById('enterprise-select').value)
                            .forEach(media => {
                                if (!selectedMedias.includes(media.id)) {
                                    optionsHtml += `<option value="${media.id}"
                                        data-url="${media.media_url}"
                                        data-type="${media.type}">
                                        ${media.name} (${media.duration}s)
                                    </option>`;
                                }
                            });
                    } else {
                        medias.forEach(media => {
                            optionsHtml += `<option value="${media.id}"
                                data-url="${media.media_url}"
                                data-type="${media.type}"
                                ${media.id === currentValue ? 'selected' : ''}>
                                ${media.name} (${media.duration}s)
                            </option>`;
                        });
                    }

                    select.innerHTML = optionsHtml;
                });
            }

            // Cập nhật position
            function updatePositions() {
                const allGroups = Array.from(document.querySelectorAll(".media-select-group"));
                allGroups.forEach((group, index) => {
                    const select = group.querySelector(".media-select");
                    const positionInput = group.querySelector(".media-position");
                    select.name = `medias[${index}][id]`;
                    positionInput.name = `medias[${index}][position]`;
                    positionInput.value = index + 1;
                    group.querySelector("label").textContent = `Media #${index + 1}`;
                });
            }

            // Đảm bảo có select box mặc định
            function ensureDefaultSelectBox() {
                const defaultSelect = mediaContainer.querySelector(".default-select");
                const allGroups = document.querySelectorAll(".media-select-group");
                if (!defaultSelect) {
                    const newPosition = allGroups.length === 0 ? 1 : parseInt(allGroups[allGroups.length - 1].querySelector(".media-position").value) + 1;
                    const newSelectGroup = createSelectBox(newPosition, true);
                    updateSelectOptions();
                    updatePositions();
                }
            }

            // Thêm nút xóa
            function addDeleteButton(selectGroup) {
                if (!selectGroup.classList.contains("default-select")) return;
                selectGroup.classList.remove("default-select");
                const container = selectGroup.querySelector("div");
                const deleteButton = document.createElement("button");
                deleteButton.type = "button";
                deleteButton.className = "btn btn-danger btn-remove";
                deleteButton.style.padding = "5px 10px";
                deleteButton.textContent = "✖";
                container.appendChild(deleteButton);
            }

            // Hiển thị video preview
            function showMediaPreview(selectElement) {
                const selectGroup = selectElement.closest("div");
                let videoContainer = selectGroup.querySelector("video");
                const selectedOption = selectElement.selectedOptions[0];

                if (selectedOption && selectedOption.value) {
                    const mediaUrl = selectedOption.getAttribute("data-url");

                    if (!videoContainer) {
                        // Nếu chưa có video, tạo mới và thêm vào div
                        videoContainer = document.createElement("video");
                        videoContainer.setAttribute("controls", "");
                        videoContainer.style.width = "80px";
                        videoContainer.style.height = "120px";

                        const source = document.createElement("source");
                        source.setAttribute("src", mediaUrl);
                        source.setAttribute("type", "video/mp4");

                        videoContainer.appendChild(source);
                        selectGroup.insertBefore(videoContainer, selectElement); // Đặt video trước select
                    } else {
                        // Nếu đã có video, chỉ cập nhật source
                        const source = videoContainer.querySelector("source");
                        source.setAttribute("src", mediaUrl);
                        videoContainer.load();
                    }
                } else {
                    // Nếu không có media nào được chọn, xóa video
                    if (videoContainer) {
                        videoContainer.remove();
                    }
                }
            }


            // Xử lý sự kiện thay đổi select
            document.addEventListener("change", function (event) {
                if (event.target.classList.contains("media-select")) {
                    const selectedValue = event.target.value;
                    const selectGroup = event.target.closest(".media-select-group");

                    selectedMedias = Array.from(document.querySelectorAll(".media-select"))
                        .map(select => select.value)
                        .filter(value => value !== "");

                    // Hiển thị preview
                    showMediaPreview(event.target);

                    if (!selectedValue) {
                        selectedMedias = selectedMedias.filter(id => id !== event.target.dataset.previousValue);
                        updateSelectOptions();
                        removeEmptySelectBoxes();
                        return;
                    }

                    event.target.dataset.previousValue = selectedValue;

                    if (selectGroup.classList.contains("default-select")) {
                        addDeleteButton(selectGroup);
                        const currentPosition = parseInt(selectGroup.querySelector(".media-position").value);
                        const newSelectGroup = createSelectBox(currentPosition + 1, true);
                    }

                    updateSelectOptions();
                    updatePositions();
                }
            });

            // Xóa select box rỗng
            function removeEmptySelectBoxes() {
                const allGroups = Array.from(document.querySelectorAll(".media-select-group:not(.default-select)"));
                for (let i = 0; i < allGroups.length; i++) {
                    const select = allGroups[i].querySelector(".media-select");
                    if (!select.value) {
                        allGroups[i].remove();
                    }
                }
                updateSelectOptions();
                updatePositions();
                ensureDefaultSelectBox();
            }

            // Xử lý nút xóa
            document.addEventListener("click", function (event) {
                if (event.target.classList.contains("btn-remove")) {
                    const selectGroup = event.target.closest(".media-select-group");
                    const selectBox = selectGroup.querySelector(".media-select");
                    const selectedValue = selectBox.value;

                    selectedMedias = selectedMedias.filter(id => id !== selectedValue);
                    selectGroup.remove();
                    updateSelectOptions();
                    updatePositions();
                    ensureDefaultSelectBox();
                }
            });

            // Danh sách media từ server
            let medias = [
                    @foreach ($medias as $media)
                {
                    id: "{{ $media->id }}",
                    name: "{{ $media->name }}",
                    duration: "{{ $media->duration }}",
                    media_url: "{{ $media->media_url }}",
                    type: "{{ $media->type }}",
                    enterprise_id: "{{ $media->enterprise_id }}",
                },
                @endforeach
            ];
            console.log(medias);

            // Khởi tạo
            selectedMedias = Array.from(document.querySelectorAll(".media-select"))
                .map(select => select.value)
                .filter(value => value !== "");
            updateSelectOptions();
            updatePositions();
            ensureDefaultSelectBox();

            // Hiển thị preview cho các media đã chọn ban đầu
            document.querySelectorAll(".media-select").forEach(select => {
                if (select.value) {
                    showMediaPreview(select);
                }
            });

            // Xử lý submit form
            const form = document.querySelector("form");
            form.addEventListener("submit", function (event) {
                document.querySelectorAll(".media-select-group").forEach(group => {
                    const select = group.querySelector(".media-select");
                    if (!select.value) {
                        group.remove();
                    }
                });
                updatePositions();
            });


            /**
             * Xử lý sự kiện thay đổi enterprise
             */
            document.getElementById('enterprise-select').addEventListener('change', function () {
                console.log('su kien change enterprise')

                let entepriseId = this.value;
                console.log(entepriseId);
                console.log(medias);
                // Lọc các media theo enterprise_id
                let filteredMedias = medias.filter(media => media.enterprise_id == entepriseId);
                console.log(filteredMedias);

                // Filter các select box media theo enterprise_id
                let mediaSelects = document.querySelectorAll('.media-select');

                // Cập nhật lại các select box media
                mediaSelects.forEach(select => {
                    select.innerHTML = '<option value="">{{__('playlist-create.select-media')}}</option>';
                    filteredMedias.forEach(media => {
                        select.innerHTML += `<option value="${media.id}" data-url="${media.media_url}" data-type="${media.type}">${media.name} (${media.duration}s)</option>`;
                    });
                });
            });
        });
    </script>
@endpush
