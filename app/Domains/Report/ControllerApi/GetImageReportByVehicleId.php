<?php

namespace App\Domains\Report\ControllerApi;

use App\Domains\Report\Service\ControllerApi\GetImageReportByVehicleId as GetImageReportByVehicleIdService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Domains\Vehicle\Model\VehicleImageReport;

/**
 * Class GetImageReportByVehicleId
 * 
 * Handles retrieval of image reports based on a date range, vehicle IDs, optional label, optional media ID, and optional get_latest flag via GET request.
 * Validates query parameters and delegates data processing to the service.
 * If get_latest=true|1 and no vehicle_ids are provided, retrieves the latest report matching media_id and label directly from VehicleImageReport.
 * 
 * API Usage:
 * - Endpoint: GET /api/report/image-reports
 * - Method: GET
 * - Headers:
 *   - Accept: application/json
 *   - Authorization: Bearer {your-token}
 * - Query Parameters:
 *   - start_date: (required unless get_latest=true|1) Start date in YYYY-MM-DD format (e.g., "2025-04-01")
 *   - end_date: (required unless get_latest=true|1) End date in YYYY-MM-DD format (e.g., "2025-04-16")
 *   - vehicle_ids: (required unless get_latest=true|1) Comma-separated vehicle IDs or single ID (e.g., "5" or "5,6,7")
 *   - label: (optional) String label to filter reports (e.g., "inspection")
 *   - media_id: (optional) Integer media ID to filter reports (e.g., "123")
 *   - get_latest: (optional) Boolean or string (true, false, 1, 0) to get latest report (e.g., "true" or "1")
 * - Responses:
 *   - 200 OK:
 *     - Single vehicle_id: [{report_id, media_id, ...}] or []
 *     - Multiple vehicle_ids: { "5": [{report_id, media_id, ...}], "6": [...] }
 *     - get_latest=true|1 without vehicle_ids: {report_id, media_id, device_id, minio_url, minio_bucket, latitude, longitude, enterprise_id, target, label, created_at}
 *   - 422 Unprocessable Entity: { "error": { validation errors } }
 *   - 404 Not Found: { "error": "One or more vehicle IDs not found" or "No report found for the given criteria" }
 *   - 500 Internal Server Error: { "error": "Failed to retrieve image reports: {message}" }
 * 
 * Example Requests:
 * ```
 * // Single vehicle_id with label and media_id
 * curl -X GET "http://127.0.0.1:8000/api/report/image-reports?start_date=2025-04-01&end_date=2025-04-16&vehicle_ids=5&label=inspection&media_id=123" \
 *   -H "Accept: application/json" \
 *   -H "Authorization: Bearer your-token"
 * 
 * // Get latest report without vehicle_ids or dates
 * curl -X GET "http://127.0.0.1:8000/api/report/image-reports?label=inspection&media_id=123&get_latest=1" \
 *   -H "Accept: application/json" \
 *   -H "Authorization: Bearer your-token"
 * ```
 */
class GetImageReportByVehicleId
{
    protected $service;

    /**
     * GetImageReportByVehicleId constructor.
     *
     * @param GetImageReportByVehicleIdService $service Service to handle report extraction logic
     */
    public function __construct(GetImageReportByVehicleIdService $service)
    {
        // Log::info('GetImageReportByVehicleId: Constructor initialized');
        $this->service = $service;
    }

    /**
     * Retrieve image reports by vehicle IDs, date range, optional label, optional media ID, and optional get_latest flag.
     *
     * Validates query parameters and either calls the service to fetch and group reports or queries VehicleImageReport directly for the latest report.
     *
     * @param Request $request The HTTP request with query parameters
     * @return JsonResponse JSON response with reports or error
     */
    public function index(Request $request): JsonResponse
    {
        // Log request data for debugging
        // Log::info('GetImageReportByVehicleId: Starting index method', [
        //     'query_params' => $request->query(),
        // ]);

        // Validate query parameters
        // Log::info('GetImageReportByVehicleId: Starting validation');

        $startTime = microtime(true);
        $validator = Validator::make($request->query(), [
            'start_date' => 'required_unless:get_latest,true,1|nullable|date_format:Y-m-d', // Required unless get_latest=true|1, must be YYYY-MM-DD
            'end_date' => 'required_unless:get_latest,true,1|nullable|date_format:Y-m-d|after_or_equal:start_date', // Required unless get_latest=true|1, must be >= start_date
            'vehicle_ids' => 'required_unless:get_latest,true,1|regex:/^\d+(,\d+)*$/', // Comma-separated positive integers, not required if get_latest=true|1
            'label' => 'nullable|string|max:255', // Optional label, string, max 255 characters
            'media_id' => 'nullable|integer|min:1', // Optional media ID, positive integer
            'get_latest' => 'nullable|in:true,false,1,0', // Optional boolean or string (true, false, 1, 0)
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            Log::error('GetImageReportByVehicleId: Validation failed', [
                'errors' => $validator->errors()->toArray(),
            ]);
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }
        $validationTime = microtime(true) - $startTime;

        // Log::info('GetImageReportByVehicleId: Validation completed', [
        //     'validation_time_seconds' => $validationTime,
        // ]);

        try {
            // Check if get_latest is true or 1 and vehicle_ids are not provided
            if (in_array($request->query('get_latest'), ['true', '1'], true) && !$request->query('vehicle_ids')) {
                // Log::info('GetImageReportByVehicleId: Processing get_latest request');

                $query = VehicleImageReport::query();

                // Apply date range filter only if start_date and end_date are provided
                if ($request->query('start_date') && $request->query('end_date')) {
                    $query->whereBetween('created_at', [
                        $request->query('start_date') . ' 00:00:00',
                        $request->query('end_date') . ' 23:59:59'
                    ]);
                }

                if ($request->query('media_id')) {
                    $query->where('media_id', $request->query('media_id'));
                }

                if ($request->query('label')) {
                    $query->where('label', $request->query('label'));
                }

                $report = $query->orderBy('created_at', 'desc')->first();

                if (!$report) {
                    // Log::info('GetImageReportByVehicleId: No report found for get_latest request');
                    return response()->json([
                        'error' => 'No report found for the given criteria',
                    ], 404);
                }

                // Format the report data
                $reportData = [
                    'report_id' => $report->id,
                    'media_id' => $report->media_id,
                    'device_id' => $report->device_id,
                    'minio_url' => $report->minio_url,
                    'minio_bucket' => $report->minio_bucket,
                    'latitude' => $report->latitude,
                    'longitude' => $report->longitude,
                    'enterprise_id' => $report->enterprise_id,
                    'target' => $report->target,
                    'label' => $report->label,
                    'created_at' => $report->created_at->toIso8601String(),
                ];

                // Log::info('GetImageReportByVehicleId: Latest report retrieved successfully');
                return response()->json($reportData, 200);
            }

            // Parse vehicle_ids from comma-separated string
            $vehicleIds = array_map('intval', explode(',', $request->query('vehicle_ids')));
            // Log::info('GetImageReportByVehicleId: Parsed vehicle IDs', [
            //     'vehicle_ids' => $vehicleIds,
            // ]);

            // Call service to fetch reports
            // Log::info('GetImageReportByVehicleId: Calling service');

            $startTime = microtime(true);
            $reports = $this->service->handle(
                $request->query('start_date'),
                $request->query('end_date'),
                $vehicleIds,
                $request->query('label'),
                $request->query('media_id')
            );
            $serviceTime = microtime(true) - $startTime;
            // Log::info('GetImageReportByVehicleId: Service completed', [
            //     'service_time_seconds' => $serviceTime,
            //     'report_count' => is_array($reports) ? count($reports) : array_sum(array_map('count', $reports)),
            // ]);

            // Return reports
            // Log::info('GetImageReportByVehicleId: Index method completed successfully');
            return response()->json($reports, 200);
        } catch (\Exception $e) {
            // Log and return error if an exception occurs
            // Log::error('GetImageReportByVehicleId: Exception occurred', [
            //     'message' => $e->getMessage(),
            //     'trace' => $e->getTraceAsString(),
            // ]);
            return response()->json([
                'error' => 'Failed to retrieve image reports: ' . $e->getMessage(),
            ], 500);
        }
    }
}