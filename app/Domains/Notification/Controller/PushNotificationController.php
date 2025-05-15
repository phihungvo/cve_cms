<?php

namespace App\Domains\Notification\Controller;

use App\Domains\Core\Controller\ControllerAbstract;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class PushNotificationController extends ControllerAbstract
{
    public function __invoke(): JsonResponse
    {
        return $this->actionPost('pushNotification');
    }

    protected function pushNotification(): JsonResponse
    {
        try {
            $notificationId = $this->request->input('notification_id');
            if (!$notificationId) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Notification ID is required',
                ], 400);
            }

            $result = $this->action()->pushNotification();

            return response()->json([
                'status' => 'success',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to push notification: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}