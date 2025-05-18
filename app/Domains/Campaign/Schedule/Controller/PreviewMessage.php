<?php declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Controller;

use App\Domains\Campaign\Schedule\Service\Controller\PreviewMessage as ControllerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreviewMessage extends ControllerAbstract
{
    public function __invoke(Request $request): JsonResponse
    {
        $scheduleId = (int)$this->request->input('schedule_id');
        $this->row($scheduleId);

        return $this->previewMessage();
    }

    protected function previewMessage(): JsonResponse
    {
        $data = ControllerService::new($this->request, $this->auth, $this->row)->data();
        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
