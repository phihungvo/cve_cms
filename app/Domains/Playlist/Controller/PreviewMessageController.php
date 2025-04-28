<?php

namespace App\Domains\Playlist\Controller;

use App\Domains\Playlist\Service\Controller\PreviewMessageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PreviewMessageController extends ControllerAbstract
{
    public function __invoke(Request $request): JsonResponse
    {
        try {
            $playlistId = (int)$this->request->get('playlist_id');
            $this->row($playlistId);
        } catch (\Exception $exception) {
            return response()->json([
                'status' => 'error',
                'message' => $exception->getMessage(),
            ]);
        }

        return $this->previewMessage();
    }

    protected function previewMessage(): JsonResponse
    {
        $data = PreviewMessageService::new($this->request, $this->auth, $this->row)->data();

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}
