<div class="modal fade" id="delete-modal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">{{ $title }}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{ $message }}</p>
                <p id="delete-model-name"></p>
                <input type="hidden" id="delete-model-id" name="model_id">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Hủy</button>
                <button type="button" class="btn btn-danger" id="confirm-delete">Xóa</button>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('confirm-delete').addEventListener('click', async function () {
        const modelIds = document.getElementById('delete-model-id').value;
        if (!modelIds) {
            showError('Không có ID được chọn để xóa');
            return;
        }

        try {
            console.log('Sending DELETE request with model_ids:', modelIds);
            const response = await $.ajax({
                url: '{{ $route }}',
                type: '{{ strtoupper($method) }}',
                data: { model_id: modelIds },
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (response.success) {
                showSuccess(response.message || 'Xóa thành công');
                $('#delete-modal').modal('hide');
                refreshTree(selectedPath);
                fetchFiles(selectedPath);
                document.getElementById('delete-model-id').value = '';
                document.getElementById('delete-model-name').innerText = '';
            } else {
                showError(response.message || 'Không thể xóa');
            }
        } catch (error) {
            showError('Lỗi khi xóa: ' + (error.responseJSON?.message || error.message));
            console.error('Delete error:', error);
        }
    });
</script>
