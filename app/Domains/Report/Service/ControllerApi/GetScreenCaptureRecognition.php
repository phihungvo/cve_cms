<?php

namespace App\Domains\Report\Service\ControllerApi;

use App\Domains\Report\Model\ViewLog as ViewLogModel;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

/**
 * Class GetScreenCaptureRecognition
 *
 * Service để xử lý logic truy xuất báo cáo chụp màn hình dựa trên các tham số cung cấp.
 * Trả về báo cáo dưới dạng mảng hoặc một object tùy thuộc vào get_latest.
 */
class GetScreenCaptureRecognition
{
    /**
     * Xử lý truy xuất báo cáo nhận diện chụp màn hình.
     *
     * @param array $params Mảng chứa các tham số tùy chọn:
     *                      - device_id (string, tùy chọn)
     *                      - serial (string, tùy chọn)
     *                      - media_filename (string, tùy chọn)
     *                      - date (string, tùy chọn, định dạng YYYY-MM-DD)
     *                      - get_latest (bool, tùy chọn, mặc định false)
     *                      - system_time (string, tùy chọn, thời gian hệ thống để so sánh khi get_latest=true)
     *                      - filter_frame_data (bool, tùy chọn, lọc frame_data không null/rỗng)
     * @return array|object Báo cáo dưới dạng mảng (nếu get_latest=false) hoặc một object (nếu get_latest=true)
     */
    public function getScreenCaptureRecognition(array $params)
    {
        // Lấy tham số với giá trị mặc định
        $deviceId = $params['device_id'] ?? null;
        $serial = $params['serial'] ?? null;
        $mediaFilename = $params['media_filename'] ?? null;
        $date = $params['date'] ?? null;
        $getLatest = isset($params['get_latest']) ? filter_var($params['get_latest'], FILTER_VALIDATE_BOOLEAN) : false;
        $filterFrameData = isset($params['filter_frame_data']) ? filter_var($params['filter_frame_data'], FILTER_VALIDATE_BOOLEAN) : false;
        $systemTime = isset($params['system_time']) ? Carbon::parse($params['system_time']) : Carbon::now();

        Log::debug('GetScreenCaptureRecognitionService: Bắt đầu xử lý', [
            'params' => $params,
            'system_time' => $systemTime->toDateTimeString(),
        ]);

        // Khởi tạo truy vấn
        $startTime = microtime(true);
        $query = ViewLogModel::query()->select([
            'id',
            'device_id',
            'serial',
            'media_filename',
            'view_count',
            'frame_data',
            'created_at',
        ]);

        // Áp dụng các bộ lọc
        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }

        if ($serial) {
            $query->where('serial', $serial);
        }

        if ($mediaFilename) {
            $query->where('media_filename', $mediaFilename);
        }

        if ($date) {
            $query->whereDate('created_at', $date);
        }

        // Lọc frame_data không null và không rỗng
        if ($filterFrameData || $getLatest) {
            $query->whereNotNull('frame_data')->where('frame_data', '!=', '');
        }

        // Ghi log truy vấn
        Log::debug('GetScreenCaptureRecognitionService: Truy vấn SQL', [
            'query' => $query->toSql(),
            'bindings' => $query->getBindings(),
        ]);

        // Xử lý logic get_latest
        $report = null;
        if ($getLatest) {
            // Sắp xếp theo độ gần với system_time, chỉ lấy bản ghi có frame_data hợp lệ
            $query->orderByRaw('ABS(TIMESTAMPDIFF(SECOND, created_at, ?)) ASC', [$systemTime]);
            $report = $query->first();
        } else {
            $report = $query->get();
        }

        // Khởi tạo kết quả
        $result = [];
        $fetchTime = microtime(true) - $startTime;

        // Xử lý kết quả
        if ($getLatest && $report) {
            $reportData = [
                'report_id' => $report->id,
                'device_id' => $report->device_id,
                'serial' => $report->serial,
                'media_filename' => $report->media_filename,
                'view_count' => $report->view_count,
                'frame_data' => $report->frame_data,
                'created_at' => $report->created_at->toDateTimeString(),
            ];
            $result = $reportData;
        } elseif (!$getLatest && $report->isNotEmpty()) {
            foreach ($report as $item) {
                $result[] = [
                    'report_id' => $item->id,
                    'device_id' => $item->device_id,
                    'serial' => $item->serial,
                    'media_filename' => $item->media_filename,
                    'view_count' => $item->view_count,
                    'frame_data' => $item->frame_data,
                    'created_at' => $item->created_at->toDateTimeString(),
                ];
            }
        }

        Log::debug('GetScreenCaptureRecognitionService: Kết quả truy vấn', [
            'report_count' => $getLatest ? ($result ? 1 : 0) : count($result),
            'fetch_time_seconds' => $fetchTime,
            'result' => $result,
        ]);

        return $result;
    }
}