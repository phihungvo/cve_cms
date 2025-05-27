<?php

declare(strict_types=1);

namespace App\Domains\Notification\ControllerApi;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Domains\Notification\Model\Notification;
use App\Domains\Notification\Model\UserNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class GetNotification extends Controller
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
            $userId = (int) $this->request->input('user_id');
            $notifications = $this->getNotifications($userId);
            return response()->json([
                'message' => 'Notifications retrieved successfully',
                'data' => $notifications,
            ], 200);
        } catch (\Exception $e) {
            Log::error('Failed to retrieve notifications: ' . $e->getMessage());
            return response()->json([
                'error' => 'Failed to retrieve notifications',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    protected function validateInput(): void
    {
        $validator = Validator::make($this->request->all(), [
            'user_id' => 'required|numeric|exists:user,id',
            'read_status' => 'nullable|string|in:read,unread,all',
            'limit' => 'nullable|integer|min:1|max:100',
            'offset' => 'nullable|integer|min:0',
        ]);

        if ($validator->fails()) {
            throw new \Illuminate\Validation\ValidationException($validator);
        }
    }

    protected function getNotifications(int $userId): array
    {
        $query = UserNotification::query()
            ->where('user_id', $userId)
            ->with([
                'notification' => function ($query) {
                    $query->with(['sender', 'enterprise']);
                }
            ]);

        // Lọc theo trạng thái đọc
        $readStatus = $this->request->input('read_status', 'all');
        if ($readStatus === 'read') {
            $query->whereNotNull('read_at');
        } elseif ($readStatus === 'unread') {
            $query->whereNull('read_at');
        }

        // Phân trang
        $limit = $this->request->input('limit', 20);
        $offset = $this->request->input('offset', 0);
        $query->take($limit)->skip($offset);

        // Sắp xếp theo thời gian tạo mới nhất
        $query->orderBy('created_at', 'desc');

        return $query->get()->map(function ($userNotification) {
            $notification = $userNotification->notification;
            return [
                'id' => $notification->id,
                'title' => $notification->title,
                'content' => $notification->content,
                'notification_type' => $notification->notification_type,
                'enterprise_id' => $notification->enterprise_id,
                'sender_id' => $notification->sender_id,
                'sender_name' => $notification->sender ? $notification->sender->name : null,
                'enterprise_name' => $notification->enterprise ? $notification->enterprise->name : null,
                'target_group' => $notification->target_group,
                'created_at' => $notification->created_at->toDateTimeString(),
                'read_at' => $userNotification->read_at ? $userNotification->read_at->toDateTimeString() : null,
            ];
        })->toArray();
    }
}