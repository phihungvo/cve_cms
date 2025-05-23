<?php declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Controller;

use App\Domains\Campaign\Schedule\Service\Controller\PushMessageToDevices as ControllerService;
use App\Services\Mqtt\MqttService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PushMessageToDevices extends ControllerAbstract
{
    public function __invoke(Request $request): JsonResponse
    {
        try{
            $scheduleId = (int)$this->request->input('schedule_id');
            $this->row($scheduleId);
            return $this->pushMessageToDevices($request);
        }catch (Exception $e){
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ],$e->getCode());
        }
    }

    protected function pushMessageToDevices(Request $request): JsonResponse
    {
        $service = ControllerService::new($request, $this->auth, $this->row, app(MqttService::class));

        try {
            $message = $service->pushMessageToDevices();

            return response()->json([
                'status' => 'success',
                'data' => $message,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ], $e->getCode());
        }
    }
}
