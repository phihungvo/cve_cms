<?php

namespace App\Domains\Notification\Controller;

use App\Domains\Notification\Action\ActionFactory;
use Illuminate\Http\JsonResponse;
use App\Domains\CoreApp\Controller\ControllerWebAbstract as ControllerAbstract;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class PushMessageController extends ControllerAbstract
{
    public function __invoke(Request $request): JsonResponse
    {
        return $this->pushMessage($request);
    }

    protected function pushMessage(Request $request): JsonResponse
    {
        try {
            $action = app(ActionFactory::class)->setDependencies(null, $request);
            $message = $action->pushMessage();

            return response()->json([
                'status' => 'success',
                'data' => $message,
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