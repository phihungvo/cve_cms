<?php

namespace App\Domains\Playlist\Controller;

use Illuminate\Http\JsonResponse;

class PushMessageController extends ControllerAbstract
{
    public function __invoke()
    {
        $response = $this->actionPost('pushMessage');

        return $response;
    }

    protected function pushMessage(): JsonResponse
    {
        try {
            $message = $this->action()->pushMessage();

            return response()->json([
                'status' => 'success',
                'data' => $message]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

    }
}
