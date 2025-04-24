<?php

namespace App\Domains\Report\Service\ControllerApi;

use App\Domains\Report\Model\ViewLog as ViewLogModel;
use Illuminate\Support\Facades\Log;

/**
 * Class GetScreenCaptureRecognition
 *
 * Service to handle the logic for extracting screen capture reports based on provided parameters.
 * Returns reports as an array or a single object based on get_latest.
 */
class GetScreenCaptureRecognition
{
    /**
     * Handle the extraction of screen capture reports.
     *
     * @param array $params Associative array of optional parameters:
     *                      - device_id (string, optional)
     *                      - serial (string, optional)
     *                      - media_filename (string, optional)
     *                      - date (string, optional, YYYY-MM-DD format)
     *                      - get_latest (bool, optional, default false)
     * @return array|object Reports as an array (if get_latest is false or not provided) or a single object (if get_latest is true)
     */
    public function getScreenCaptureRecognition(array $params)
    {
        // Extract parameters with defaults
        $deviceId = $params['device_id'] ?? null;
        $serial = $params['serial'] ?? null;
        $mediaFilename = $params['media_filename'] ?? null;
        $date = $params['date'] ?? null;
        $getLatest = isset($params['get_latest']) ? filter_var($params['get_latest'], FILTER_VALIDATE_BOOLEAN) : false;

        // Log::info('GetScreenCaptureRecognitionService: Starting getScreenCaptureRecognition method', [
        //     'params' => $params,
        // ]);

        // Initialize query
        $startTime = microtime(true);
        $query = ViewLogModel::query();

        // Apply filters based on provided parameters
        if ($deviceId) {
            $query->where('device_id', $deviceId);
        }

        if ($serial) {
            $query->where('serial', $serial);
        }

        if ($mediaFilename) {
            $query->where('media_filename', $mediaFilename);
        }

        // Handle date filter
        if ($date) {
            $query->whereDate('created_at', $date);
        }

        // Handle get_latest logic
        if ($getLatest && $date) {
            // Get the earliest report for the specified date instead of the latest
            $query->oldest('created_at')->take(1);
        }

        // Fetch reports
        $reports = $query->get([
            'id',
            'device_id',
            'serial',
            'media_filename',
            'view_count',
            'frame_data',
        ]);

        // Initialize result
        $result = [];

        // Process reports
        if ($reports->isNotEmpty()) {
            foreach ($reports as $report) {
                $reportData = [
                    'report_id' => $report->id,
                    'device_id' => $report->device_id,
                    'serial' => $report->serial,
                    'media_filename' => $report->media_filename,
                    'view_count' => $report->view_count,
                    'frame_data' => $report->frame_data,
                ];

                $result[] = $reportData;
            }
        }

        $fetchTime = microtime(true) - $startTime;
        // Log::info('GetScreenCaptureRecognitionService: Reports fetched', [
        //     'report_count' => count($result),
        //     'fetch_time_seconds' => $fetchTime,
        // ]);

        // Log::info('GetScreenCaptureRecognitionService: Method completed successfully');

        // Return single object if get_latest is true, otherwise return array
        return $getLatest && !empty($result) ? $result[0] : $result;
    }
}
