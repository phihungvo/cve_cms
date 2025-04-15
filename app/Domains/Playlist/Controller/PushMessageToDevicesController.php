<?php

namespace App\Domains\Playlist\Controller;

use Illuminate\Http\JsonResponse;

class PushMessageToDevicesController extends ControllerAbstract
{
    public function __invoke(): JsonResponse
    {
        $response = $this->actionPost('pushMessageToDevices');

        return $response;
    }

    protected function pushMessageToDevices(): JsonResponse
    {
        try {
            $message = $this->action()->pushMessageToDevices();

            return response()->json([
                'status' => 'success',
                'data' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }
}
