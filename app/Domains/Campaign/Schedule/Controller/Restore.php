<?php
declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Controller;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class Restore extends ControllerAbstract
{
    public function __invoke(Request $request, int $id): Response|RedirectResponse
    {
        // Tìm Schedule đã bị soft delete với ID tương ứng
        $schedule = Schedule::withTrashed()->find($id);

        // Kiểm tra nếu không tìm thấy
        if (!$schedule) {
            Log::info('Không tìm thấy lịch trình với ID này.');

            return $this->redirectBackWithError('Không tìm thấy lịch trình với ID này.');
        }

        // Kiểm tra nếu chưa bị soft delete
        if (!$schedule->trashed()) {
            Log::info('Lịch trình này chưa bị xóa.');

            return $this->redirectBackWithError('Lịch trình này chưa bị xóa.');
        }

        try {
            // Khôi phục bằng cách đặt deleted_at về null
            $schedule->restore();

            $this->sessionMessage('success', __('schedule-update.restore-success'));

            return redirect()->route('schedule.index');
        } catch (\Exception $e) {
            return $this->redirectBackWithError('Có lỗi xảy ra khi khôi phục lịch trình: '.$e->getMessage());
        }
    }

    // Helper method để redirect back với thông báo lỗi
    private function redirectBackWithError(string $message): RedirectResponse
    {
        return redirect()
            ->back()
            ->with('error', $message);
    }
}
