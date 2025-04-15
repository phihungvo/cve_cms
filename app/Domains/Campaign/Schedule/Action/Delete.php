<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Action;

use App\Domains\Campaign\Schedule\Model\Schedule;
use Exception;
use DateTime;
use Illuminate\Support\Facades\Auth;

class Delete extends ActionAbstract
{
    /**
     * Thực thi xóa một schedule detail
     *
     * @param int|string $id
     * @param mixed $user Authenticated user
     *
     * @return array
     */
    public function handle($id, $user): array
    {
        try {
            // Tìm schedule theo ID
            $this->row = Schedule::query()->findOrFail($id);

            // Lấy thông tin user và permissions
            $user = Auth::user();
            $userId = $user->id;
            $userPermission = session('userPermission_'.$userId, []);
            $allPermissions = $userPermission['all'] ?? [];
            // Kiểm tra quyền root
            if (isset($allPermissions['root'])) {
                // Nếu có quyền root, xóa luôn không cần kiểm tra thời gian
                $this->delete();

                return [
                    'success' => true,
                    'message' => __('schedule-delete.delete-success'),
                ];
            }

            // Logic cũ cho user không có quyền root
            $currentTime = new DateTime();

            // Kiểm tra nếu thời gian hiện tại nằm trong khoảng start_time và end_time
            if ($this->row->start_time <= $currentTime && $currentTime <= $this->row->end_time) {
                return [
                    'success' => false,
                    'message' => __('schedule-delete.delete-not-allowed-during-active-period'),
                ];
            }

            // Xóa schedule nếu không nằm trong khoảng thời gian hoạt động
            $this->delete();

            return [
                'success' => true,
                'message' => __('schedule-delete.delete-success'),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => __('schedule-delete.delete-error'),
                'error' => $e->getMessage(),
                'code' => $e->getCode() ?: 500,
            ];
        }
    }

    /**
     * Xóa schedule detail
     *
     * @return void
     */
    protected function delete(): void
    {
        $this->row->delete();
    }
}
