<?php

namespace App\Domains\Report\ControllerApi;

use App\Domains\Report\Service\ControllerApi\GetScreenCaptureRecognition as GetScreenCaptureRecognitionService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class GetScreenCaptureRecognition
{
    protected $service;

    /**
     * GetImageReportByVehicleId constructor.
     *
     * @param GetScreenCaptureRecognitionService $service Service to handle report extraction logic
     */
    public function __construct(GetScreenCaptureRecognitionService $service)
    {
        // Log::info('GetScreenCaptureRecognition: Constructor initialized');
        $this->service = $service;
    }

    /**
     * Retrieve image reports based on query parameters.
     *
     * Validates query parameters and calls the service to fetch reports.
     * Returns raw report data as JSON (single object or array) or empty object if no results.
     *
     * @param Request $request The HTTP request with query parameters
     * @return JsonResponse JSON response with reports or empty object
     */
    public function index(Request $request): JsonResponse
    {
        try {
            // Define validation rules for optional parameters
            $validator = Validator::make($request->query(), [
                'device_id' => 'nullable|string',
                'serial' => 'nullable|string',
                'media_filename' => 'nullable|string',
                'date' => 'nullable|date_format:Y-m-d',
                'get_latest' => 'nullable|in:true,false,1,0',
            ]);

            // Check if validation fails
            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors(),
                ], 422);
            }

            // Extract validated parameters
            $params = $validator->validated();

            // Convert get_latest to boolean for the service
            if (isset($params['get_latest'])) {
                $params['get_latest'] = in_array($params['get_latest'], ['true', '1'], true);
            }

            // Call the service with the provided parameters
            $reports = $this->service->getScreenCaptureRecognition($params);

            // Return raw reports (single object or array) or empty object if no results
            return response()->json($reports ?: []);
        } catch (\Exception $e) {
            // Log the error
            Log::error('Error in GetScreenCaptureRecognition: ' . $e->getMessage());

            // Return error response
            return response()->json([
                'status' => 'error',
                'message' => 'An error occurred while retrieving reports',
            ], 500);
        }
    }
}