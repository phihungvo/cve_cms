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

class SendImageReport
{
    protected $sendImageReportService;

    public function __construct(SendImageReportService $sendImageReportService)
    {
        Log::info('SendImageReportController: Constructor initialized');
        $this->sendImageReportService = $sendImageReportService;
    }

    public function store(Request $request): JsonResponse
    {
        Log::info('SendImageReportController: Starting store method', [
            'request_data' => $request->all(),
            'files' => $request->hasFile('image') ? $request->file('image')->getPathname() : null,
        ]);

        // Validation
        Log::info('SendImageReportController: Starting validation');
        $startTime = microtime(true);
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg',
            'media_id' => 'required|integer',
            'device_id' => 'nullable|integer',
            'vehicle_id' => 'nullable|integer',
            'minio_url' => 'required|string',
            'minio_bucket' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'target' => 'nullable|integer',
        ]);

        if ($validator->fails()) {
            Log::error('SendImageReportController: Validation failed', [
                'errors' => $validator->errors()->toArray(),
            ]);
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }
        $validationTime = microtime(true) - $startTime;
        Log::info('SendImageReportController: Validation completed', [
            'validation_time_seconds' => $validationTime,
        ]);

        try {
            // Lấy enterprise_id từ các bảng
            Log::info('SendImageReportController: Fetching enterprise IDs');
            $startTime = microtime(true);
            $media = Media::find($request->media_id);
            $device = $request->device_id ? Device::find($request->device_id) : null;
            $vehicle = $request->vehicle_id ? Vehicle::find($request->vehicle_id) : null;
            $fetchTime = microtime(true) - $startTime;
            Log::info('SendImageReportController: Enterprise IDs fetched', [
                'media_id' => $request->media_id,
                'device_id' => $request->device_id,
                'vehicle_id' => $request->vehicle_id,
                'fetch_time_seconds' => $fetchTime,
            ]);

            // Kiểm tra media_id tồn tại
            if (!$media) {
                Log::error('SendImageReportController: Invalid media_id', [
                    'media_id' => $request->media_id,
                ]);
                return response()->json([
                    'error' => 'Invalid media_id',
                ], 404);
            }

            // Kiểm tra device_id và vehicle_id nếu được gửi
            if ($request->device_id && !$device) {
                Log::error('SendImageReportController: Invalid device_id', [
                    'device_id' => $request->device_id,
                ]);
                return response()->json([
                    'error' => 'Invalid device_id',
                ], 404);
            }
            if ($request->vehicle_id && !$vehicle) {
                Log::error('SendImageReportController: Invalid vehicle_id', [
                    'vehicle_id' => $request->vehicle_id,
                ]);
                return response()->json([
                    'error' => 'Invalid vehicle_id',
                ], 404);
            }

            // Lấy enterprise_id từ media
            $mediaEnterpriseId = $media->enterprise_id;
            $deviceEnterpriseId = $device ? $device->enterprise_id : null;
            $vehicleEnterpriseId = $vehicle ? $vehicle->enterprise_id : null;
            Log::info('SendImageReportController: Enterprise IDs retrieved', [
                'media_enterprise_id' => $mediaEnterpriseId,
                'device_enterprise_id' => $deviceEnterpriseId,
                'vehicle_enterprise_id' => $vehicleEnterpriseId,
            ]);

            // So sánh enterprise_id nếu có
            if (
                ($deviceEnterpriseId && $mediaEnterpriseId !== $deviceEnterpriseId) ||
                ($vehicleEnterpriseId && $mediaEnterpriseId !== $vehicleEnterpriseId)
            ) {
                Log::error('SendImageReportController: Enterprise IDs do not match', [
                    'media_enterprise_id' => $mediaEnterpriseId,
                    'device_enterprise_id' => $deviceEnterpriseId,
                    'vehicle_enterprise_id' => $vehicleEnterpriseId,
                ]);
                return response()->json([
                    'error' => 'Enterprise IDs do not match',
                ], 422);
            }

            // Gọi service với dữ liệu và enterprise_id
            Log::info('SendImageReportController: Calling SendImageReportService');
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
                    ]),
                    ['enterprise_id' => $mediaEnterpriseId]
                )
            );
            $serviceTime = microtime(true) - $startTime;
            Log::info('SendImageReportController: SendImageReportService completed', [
                'service_time_seconds' => $serviceTime,
                'result_id' => $result->id ?? null,
            ]);

            Log::info('SendImageReportController: Store method completed successfully');
            return response()->json([
                'message' => 'Image report created successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
            Log::error('SendImageReportController: Exception occurred', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return response()->json([
                'error' => 'Failed to process image report: ' . $e->getMessage(),
            ], 500);
        }
    }
}