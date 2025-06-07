<?php

namespace App\Domains\Report\ControllerApi;

use App\Domains\Report\Service\ControllerApi\SaveImageNodeRed as SendImageReportService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class SaveImageNodeRed
{
    protected $sendImageReportService;

    public function __construct(SendImageReportService $sendImageReportService)
    {
        $this->sendImageReportService = $sendImageReportService;
    }

    public function store(Request $request): JsonResponse
    {
        // Ensure JSON content type
        if ($request->isJson()) {
            $data = $request->json()->all();
        } else {
            Log::error('SendImageReport: Invalid content type, expecting JSON');
            return response()->json([
                'error' => 'Invalid content type, expecting application/json',
            ], 415);
        }

        // Validate input data
        $validator = Validator::make($data, [
            'image' => 'required|string|max:20971520', // Limit ~20MB
            'file_name' => 'required|string|regex:/^[a-zA-Z0-9_\-\.]+$/',
        ]);

        if ($validator->fails()) {
            Log::error('SendImageReport: Validation failed', [
                'errors' => $validator->errors()->toArray(),
            ]);
            return response()->json([
                'error' => $validator->errors(),
            ], 422);
        }


        try {
            // Prepare data for service
            $imageData = [
                'base64' => $data['image'],
                'file_name' => $data['file_name'],
            ];

            // Call service to handle image upload
            $result = $this->sendImageReportService->handle($imageData);


            return response()->json([
                'message' => 'Image report created successfully',
                'data' => $result,
            ], 201);
        } catch (\Exception $e) {
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