<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Controller;

use App\Domains\Cvedixrt\Event\Service\Controller\EventBroadcast as ControllerService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;

class EventBroadcastController extends ControllerAbstract
{
    public function __invoke(): JsonResponse
    {

        if ($response = $this->actions()) {
            return $response;
        }

        return response()->json(['error' => 'Unable to process request'], 400);
    }

    protected function actions()
    {
        return $this->actionPost('sendSingleEvent')
            ?: $this->actionPost('sendEvents');
    }

    protected function sendSingleEvent(): JsonResponse
    {
        try {
            $this->action(domain: "Cvedixrt\Event")->sendSingleEvent($this->dataSingle()->toArray());

            return response()->json([
                'success' => 'ok',
                'message' => 'Event has been sent.',
            ]);
        } catch (Exception $e) {
            return response()->json(
                [
                    'success' => 'error',
                    'message' => $e->getMessage(),
                ],
                500
            );
        }
    }

    protected function sendEvents(): JsonResponse
    {
        try {
            $this->action(domain: "Cvedixrt\Event")->sendEvents($this->data()->toArray());

            return response()->json([
                'success' => 'ok',
                'message' => 'Events have been sent.',
            ]);
        } catch (Exception $exception) {
            return response()->json(
                [
                    'success' => 'error',
                    'message' => $exception->getMessage(),
                ],
                500
            );
        }
    }

    protected function data(): Collection
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }

    protected function dataSingle()
    {
        return ControllerService::new($this->request, $this->auth)->dataSingle();
    }
}
