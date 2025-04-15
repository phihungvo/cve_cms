<?php

namespace App\Domains\Campaign\Schedule\Controller;

use App\Domains\Campaign\Schedule\Service\Controller\PushMessageToDevices as ControllerService;
use App\Services\Mqtt\MqttService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushMessageToDevices extends ControllerAbstract
{
    public function __invoke(Request $request)
    {
        return $this->pushMessageToDevices($request);
    }

    protected function pushMessageToDevices(Request $request): JsonResponse
    {
        $service = ControllerService::new($request, $this->auth, app(MqttService::class));

        try {
            $message = $service->pushMessageToDevices();

            return response()->json([
                'status' => 'success',
                'data' => $message,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], 500);
        }
    }
}
