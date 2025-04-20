<?php

namespace App\Domains\Report\ControllerApi;

use App\Domains\Report\Service\ControllerApi\SendImageReport as SendImageReportService;
use App\Domains\Campaign\Media\Model\Media;
use App\Domains\Device\Model\Device;
use App\Domains\Vehicle\Model\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

/**
 * Class SendImageReport
 * 
 * Handles the creation of image reports by uploading images to MinIO storage
 * and storing metadata in the database. Validates input data, checks enterprise
 * consistency, and delegates processing to the SendImageReportService.
 * 
 * API Usage:
 * - Endpoint: POST /api/report/send-image-report
 * - Method: POST
 * - Headers:
 *   - Accept: application/json
 *   - Authorization: Bearer {your-token} (if authentication is required)
 * - Body (multipart/form-data):
 *   - image: (required) Image file (jpeg, png, jpg)
 *   - media_id: (required unless label is 'odo') Integer, ID of the media
 *   - device_id: (optional) Integer, ID of the device
 *   - vehicle_id: (optional) Integer, ID of the vehicle
 *   - minio_url: (required) String, path to store image in MinIO (e.g., goads/image-driver-report/18/10.png)
 *   - minio_bucket: (required) String, MinIO bucket name (e.g., media)
 *   - latitude: (required) Numeric, latitude coordinate
 *   - longitude: (required) Numeric, longitude coordinate
 *   - target: (optional) Integer, target value for the report
 *   - source_type: (optional) String, source of the report (e.g., driver, system)
 *   - label: (optional) String, label of the report (e.g., odo, screen,..)
 * - Responses:
 *   - 201 Created: { "message": "Image report created successfully", "data": { ... } }
 *   - 422 Unprocessable Entity: { "error": { validation errors } }
 *   - 404 Not Found: { "error": "Invalid media_id" | "Invalid device_id" | "Invalid vehicle_id" }
 *   - 422 Unprocessable Entity: { "error": "Enterprise IDs do not match" }
 *   - 500 Internal Server Error: { "error": "Failed to process image report: {message}" }
 * 
 * Example Request:
 * ```
 * curl -X POST http://127.0.0.1:8000/api/report/send-image-report \
 *   -H "Accept: application/json" \
 *   -H "Authorization: Bearer your-token" \
 *   -F "image=@/path/to/image.jpg" \
 *   -F "media_id=45" \
 *   -F "device_id=11" \
 *   -F "vehicle_id=5" \
 *   -F "minio_url=goads/image-driver-report/18/10.png" \
 *   -F "minio_bucket=media" \
 *   -F "latitude=10.124" \
 *   -F "longitude=10.11" \
 *   -F "target=6" \
 *   -F "source_type=driver" \
 *   -F "label=odo"
 * ```
 */
class SendImageReport
{
    protected $sendImageReportService;

    /**
     * SendImageReport constructor.
     *
     * @param SendImageReportService $sendImageReportService Service to handle image upload and database operations
     */
    public function __construct(SendImageReportService $sendImageReportService)
    {
        $this->sendImageReportService = $sendImageReportService;
    }

    /**
     * Store a new image report.
     *
     * Validates the request, checks enterprise ID consistency across media, device, and vehicle,
     * and delegates image upload and database storage to the service.
     *
     * @param Request $request The HTTP request containing image and metadata
     * @return JsonResponse JSON response with success message or error
     */
    public function store(Request $request): JsonResponse
    {
        // Validate input data
        $startTime = microtime(true);
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg',
            'media_id' => ['integer', 'required_unless:label,odo'],
            'device_id' => 'nullable|integer',
            'vehicle_id' => 'nullable|integer',
            'minio_url' => 'required|string',
            'minio_bucket' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'target' => 'nullable|integer',
            'source_type' => 'nullable|string',
            'label' => 'nullable|string',
        ]);

        // Return validation errors if any
        if ($validator->fails()) {
            Log::error('SendImageReport: Validation failed', [
                'errors' => $validator->errors()->toArray(),
            ]);
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }
        $validationTime = microtime(true) - $startTime;

        try {
            // Initialize variables
            $media = null;
            $device = null;
            $vehicle = null;
            $mediaEnterpriseId = null;
            $deviceEnterpriseId = null;
            $vehicleEnterpriseId = null;

            // Check if label is 'odo'
            $isOdoLabel = $request->input('label') === 'odo';

            // Fetch enterprise IDs from Media, Device, and Vehicle tables
            $startTime = microtime(true);
            if (!$isOdoLabel) {
                $media = Media::find($request->media_id);
                if (!$media) {
                    Log::error('SendImageReport: Invalid media_id', [
                        'media_id' => $request->media_id,
                    ]);
                    return response()->json([
                        'error' => 'Invalid media_id',
                    ], 404);
                }
                $mediaEnterpriseId = $media->enterprise_id;
            }

            $device = $request->device_id ? Device::find($request->device_id) : null;
            $vehicle = $request->vehicle_id ? Vehicle::find($request->vehicle_id) : null;
            $fetchTime = microtime(true) - $startTime;

            // Check if device_id exists (if provided)
            if ($request->device_id && !$device) {
                Log::error('SendImageReport: Invalid device_id', [
                    'device_id' => $request->device_id,
                ]);
                return response()->json([
                    'error' => 'Invalid device_id',
                ], 404);
            }

            // Check if vehicle_id exists (if provided)
            if ($request->vehicle_id && !$vehicle) {
                Log::error('SendImageReport: Invalid vehicle_id', [
                    'vehicle_id' => $request->vehicle_id,
                ]);
                return response()->json([
                    'error' => 'Invalid vehicle_id',
                ], 404);
            }

            // Get enterprise IDs from device and vehicle
            $deviceEnterpriseId = $device ? $device->enterprise_id : null;
            $vehicleEnterpriseId = $vehicle ? $vehicle->enterprise_id : null;

            // Determine enterprise_id for the report
            $enterpriseId = $isOdoLabel ? ($deviceEnterpriseId ?? $vehicleEnterpriseId) : $mediaEnterpriseId;

            // If no enterprise_id can be determined, throw an error
            if (!$enterpriseId) {
                Log::error('SendImageReport: No valid enterprise ID found', [
                    'media_id' => $request->media_id,
                    'device_id' => $request->device_id,
                    'vehicle_id' => $request->vehicle_id,
                    'label' => $request->label,
                ]);
                return response()->json([
                    'error' => 'No valid enterprise ID found',
                ], 422);
            }

            // Verify enterprise ID consistency (skip media_id check if label is 'odo')
            if (
                ($deviceEnterpriseId && $enterpriseId !== $deviceEnterpriseId) ||
                ($vehicleEnterpriseId && $enterpriseId !== $vehicleEnterpriseId)
            ) {
                Log::error('SendImageReport: Enterprise IDs do not match', [
                    'media_enterprise_id' => $mediaEnterpriseId,
                    'device_enterprise_id' => $deviceEnterpriseId,
                    'vehicle_enterprise_id' => $vehicleEnterpriseId,
                ]);
                return response()->json([
                    'error' => 'Enterprise IDs do not match',
                ], 422);
            }

            // Call service to handle image upload and database storage
            $startTime = microtime(true);
            $result = $this->sendImageReportService->handle(
                $request->file('image'),
                array_merge(
                    $request->only([
                        'media_id',
                        'device_id',
                        'vehicle_id',
                        'minio_url',
                        'minio_bucket',
                        'latitude',
                        'longitude',
                        'target',
                        'source_type',
                        'label'
                    ]),
                    ['enterprise_id' => $enterpriseId]
                )
            );
            $serviceTime = microtime(true) - $startTime;

            // Return success response
            return response()->json([
                'message' => 'Image report created successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            // Log and return error if an exception occurs
            Log::error('SendImageReport: Exception occurred', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Failed to process image report: ' . $e->getMessage(),
            ], 500);
        }
    }
}