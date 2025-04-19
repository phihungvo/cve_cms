<?php

namespace App\Domains\Report\ControllerApi;

use App\Domains\Report\Service\ControllerApi\GetImageReportByVehicleId as GetImageReportByVehicleIdService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Class GetImageReportByVehicleId
 * 
 * Handles retrieval of image reports based on a date range and vehicle IDs via GET request.
 * Validates query parameters and delegates data processing to the service.
 * 
 * API Usage:
 * - Endpoint: GET /api/report/image-reports
 * - Method: GET
 * - Headers:
 *   - Accept: application/json
 *   - Authorization: Bearer {your-token}
 * - Query Parameters:
 *   - start_date: (required) Start date in YYYY-MM-DD format (e.g., "2025-04-01")
 *   - end_date: (required) End date in YYYY-MM-DD format (e.g., "2025-04-16")
 *   - vehicle_ids: (required) Comma-separated vehicle IDs or single ID (e.g., "5" or "5,6,7")
 * - Responses:
 *   - 200 OK:
 *     - Single vehicle_id: [{report_id, media_id, ...}] or []
 *     - Multiple vehicle_ids: { "5": [{report_id, media_id, ...}], "6": [...] }
 *   - 422 Unprocessable Entity: { "error": { validation errors } }
 *   - 404 Not Found: { "error": "One or more vehicle IDs not found" }
 *   - 500 Internal Server Error: { "error": "Failed to retrieve image reports: {message}" }
 * 
 * Example Requests:
 * ```
 * // Single vehicle_id
 * curl -X GET "http://127.0.0.1:8000/api/report/image-reports?start_date=2025-04-01&end_date=2025-04-16&vehicle_ids=5" \
 *   -H "Accept: application/json" \
 *   -H "Authorization: Bearer your-token"
 * 
 * // Multiple vehicle_ids
 * curl -X GET "http://127.0.0.1:8000/api/report/image-reports?start_date=2025-04-01&end_date=2025-04-16&vehicle_ids=5,6" \
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
     * Retrieve image reports by vehicle IDs and date range.
     *
     * Validates query parameters and calls the service to fetch and group reports.
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
            'start_date' => 'required|date_format:Y-m-d', // Start date must be YYYY-MM-DD
            'end_date' => 'required|date_format:Y-m-d|after_or_equal:start_date', // End date must be >= start_date
            'vehicle_ids' => 'required|regex:/^\d+(,\d+)*$/', // Comma-separated positive integers
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
                $vehicleIds
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