<?php

declare(strict_types=1);

namespace App\Domains\Notification\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Domains\Notification\Model\UserNotification;
use App\Domains\Notification\Model\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationMarkAsRead extends Controller
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function __invoke(): JsonResponse
    {
        try {
            $this->validateInput();
            $notificationId = (int) $this->request->query('notification_id');
            $userId = Auth::id(); // Lấy user_id từ người dùng đã xác thực
            if (!$userId) {
                throw new \Exception('User not authenticated');
            }
            $readAt = $this->request->query('read_at', now()->toDateTimeString()); // Lấy read_at từ query, mặc định là now()
            $this->markAsRead($userId, $notificationId, $readAt);
            return response()->json([
                'message' => 'Notification marked as read successfully',
                'notification_id' => $notificationId,
                'read_at' => $readAt,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to mark notification as read: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to mark notification as read',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    protected function validateInput(): void
    {
        $validator = Validator::make($this->request->query(), [
            'notification_id' => 'required|numeric|exists:notification,id',
            'read_at' => 'nullable|date', // read_at là tùy chọn và phải là định dạng ngày hợp lệ
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    protected function markAsRead(int $userId, int $notificationId, string $readAt): void
    {
        // Kiểm tra notification có tồn tại không
        $notification = Notification::find($notificationId);
        if (!$notification) {
            throw new \Exception('Notification not found');
        }

        // Tìm hoặc tạo bản ghi trong user_notification
        $userNotification = UserNotification::firstOrCreate(
            [
                'user_id' => $userId,
                'notification_id' => $notificationId,
            ],
            [
                'read_at' => Carbon::parse($readAt),
            ]
        );

        // Nếu bản ghi đã tồn tại và chưa được đánh dấu là đã đọc, cập nhật read_at
        if ($userNotification->wasRecentlyCreated === false && !$userNotification->read_at) {
            $userNotification->update([
                'read_at' => Carbon::parse($readAt),
            ]);
        }
    }
}