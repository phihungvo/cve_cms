<?php

namespace App\Domains\Notification\Controller;

use App\Domains\Core\Controller\ControllerAbstract;
use App\Domains\Notification\Action\ResendNotificationAction;
use Illuminate\Http\JsonResponse;

class ResendNotificationController extends ControllerAbstract
{
    public function resend(int $notificationId): JsonResponse
    {
        try {
            $action = new ResendNotificationAction();
            $result = $action->handle($notificationId);

            return response()->json([
                'status' => 'success',
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}