<?php

namespace App\Domains\Notification\Controller;

use App\Domains\Core\Controller\ControllerAbstract;
use Illuminate\Http\JsonResponse;

class PushNotificationToDevicesController extends ControllerAbstract
{
    public function __invoke(): JsonResponse
    {
        return $this->actionPost('pushNotificationToDevices');
    }

    protected function pushNotificationToDevices(): JsonResponse
    {
        try {
            $result = $this->action()->pushNotificationToDevices();

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