<?php declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Controller;

use App\Domains\Campaign\Schedule\Service\Controller\PushMessage as ControllerService;
use App\Services\Mqtt\MqttService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PushMessage extends ControllerAbstract
{
    public function __invoke(Request $request)
    {
        try {
            $scheduleId = (int)$this->request->input('schedule_id');
            $this->row($scheduleId);
            return $this->pushMessage($request);
        }catch (Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ],$e->getCode());
        }

    }

    protected function pushMessage(Request $request): JsonResponse
    {
        $service = ControllerService::new($request, $this->auth, $this->row, app(MqttService::class));

        try {
            $message = $service->pushMessage();

            return response()->json([
                'status' => 'success',
                'data' => $message,
            ]);
        } catch (\Exception $e) {
            Log::error('Validation failed: ', $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => __('schedule-update.push-message-error'),
            ], 500);
        }
    }
}
