<?php

namespace App\Domains\Notification\Controller;

use App\Domains\Notification\Action\ActionFactory;
use Illuminate\Http\JsonResponse;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Http\Request;

class PushMessageToDevicesController extends ControllerAbstract
{
    public function __invoke(Request $request): JsonResponse
    {
        return $this->pushMessageToDevices($request);
    }

    protected function pushMessageToDevices(Request $request): JsonResponse
    {
        try {
            $action = app(ActionFactory::class)->setDependencies(null, $request);
            $message = $action->pushMessageToDevices();

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