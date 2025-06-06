<?php declare(strict_types=1);

namespace App\Domains\Device\Controller;

use FFMpeg\FFMpeg;
use Illuminate\Http\JsonResponse;

class RTAnalyticsCheckRTSPStatus extends ControllerAbstract
{
    public function __invoke(int $id): JsonResponse
    {
        return $this->actionPost('checkRTSPStatus');
    }

    protected function checkRTSPStatus(): JsonResponse
    {
        $url = $this->request->input('url');
        if (!$url || !str_starts_with($url, 'rtsp://')) {
            return response()->json(['status' => 'error', 'message' => 'Invalid RTSP URL'], 400);
        }
        try {
            // Khởi tạo FFmpeg
            $ffmpeg = FFMpeg::create();
            $ffprobe = $ffmpeg->getFFProbe();

            // Kiểm tra xem luồng RTSP có hợp lệ không
            $probe = $ffprobe->streams($url)->first();

            if ($probe) {
                return response()->json(['status' => 'success']);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Cannot connect to RTSP stream']);
            }
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}
