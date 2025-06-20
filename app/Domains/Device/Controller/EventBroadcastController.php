<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use App\Domains\Device\Service\Controller\EventBroadcast as ControllerService;
use App\Exceptions\NotFoundException;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class EventBroadcastController extends ControllerAbstract
{
    public function __invoke(int $id)
    {
        try {
            $this->row($id);
        } catch (NotFoundException $e) {
            return response()->json(['error' => $e->getMessage()], 404);
        }

        if ($response = $this->actionPost('sendEvents')) {
            return $response;
        }

        return response()->json(['error' => 'Unable to process request'], 400);
    }

    protected function sendEvents(): JsonResponse
    {
        try {
            $this->action(row: $this->row, domain: null, data: $this->data()->toArray())->sendEvents();

            return $this->json(
                [
                    'success' => 'ok',
                    'message' => 'Events have been sent.',
                ]
            );
        } catch (Exception $e) {
            return $this->json(
                [
                    'success' => 'error',
                    'message' => $e->getMessage(),
                ],
                500
            );
        }
    }

    protected function data(): Collection
    {
        return ControllerService::new($this->request, $this->auth, $this->row)->data();
    }
}
