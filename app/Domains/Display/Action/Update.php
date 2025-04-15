<?php
declare(strict_types=1);

namespace App\Domains\Display\Action;

use App\Domains\Display\Model\Display;
use Illuminate\Support\Facades\Log;

class Update
{
    protected array $data;

    protected Display $row;

    public function handle(Display $display, array $data): array
    {
        $this->row = $display;
        $this->data = $data;

        return $this->updateDisplay();
    }

    protected function updateDisplay(): array
    {
        // Khởi tạo mảng dữ liệu cần cập nhật
        $dataToUpdate = [];

        // Kiểm tra và thêm schedule_published nếu có trong data
        if (isset($this->data['schedule_published'])) {
            $dataToUpdate['schedule_published'] = $this->data['schedule_published'];
        }

        // Kiểm tra và thêm playlist_published nếu có trong data
        if (isset($this->data['playlist_published'])) {
            $dataToUpdate['playlist_published'] = $this->data['playlist_published'];
        }

        // Nếu không có dữ liệu để cập nhật
        if (empty($dataToUpdate)) {
            return [
                'message' => 'Fail',
                'error' => 'No valid data to update',
            ];
        }

        // Log::info('Updating display with data: ', $dataToUpdate);

        // Thực hiện cập nhật và kiểm tra kết quả
        $updated = $this->row->update($dataToUpdate);

        if ($updated) {
            return [
                'message' => 'Successfully',
            ];
        }

        return [
            'message' => 'Fail',
            'error' => 'Update operation failed',
        ];
    }
}
