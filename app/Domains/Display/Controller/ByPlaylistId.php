<?php

namespace App\Domains\Display\Controller;

use Illuminate\Http\Request;

class ByPlaylistId extends ControllerAbstract
{
    public function __invoke(Request $request)
    {
        return $this->getDisplayByPlaylistId($request);
    }

    protected function getDisplayByPlaylistId(Request $request): \Illuminate\Http\JsonResponse
    {
        $service = \App\Domains\Display\Service\Controller\ByPlaylistId::new($request, $this->auth);

        try {
            $displays = $service->getDisplayByPlaylistId();

            return response()->json([
                'status' => 'success',
                'data' => $displays,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ], 500);
        }
    }
}
