<?php
declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Controller;

use App\Domains\User\Enterprise\Billing\Model\Billing;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class Restore extends ControllerAbstract
{
    public function __invoke(Request $request, int $id): Response|RedirectResponse
    {
        // Tìm eservice đã bị soft delete với ID tương ứng
        $eservice = Billing::withTrashed()->find($id);

        // Kiểm tra nếu không tìm thấy
        if (!$eservice) {
            Log::info('Không tìm thấy dịch vụ với ID này.');

            return $this->redirectBackWithError('Không tìm thấy dịch vụ với ID này.');
        }

        // Kiểm tra nếu chưa bị soft delete
        if (!$eservice->trashed()) {
            Log::info('dịch vụ này chưa bị xóa.');

            return $this->redirectBackWithError('dịch vụ này chưa bị xóa.');
        }

        try {
            // Khôi phục bằng cách đặt deleted_at về null
            $eservice->restore();

            $this->sessionMessage('success', __('license-update.restore-success'));

            return redirect()->route('license.index');
        } catch (\Exception $e) {
            return $this->redirectBackWithError('Có lỗi xảy ra khi khôi phục dịch vụ: ' . $e->getMessage());
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
