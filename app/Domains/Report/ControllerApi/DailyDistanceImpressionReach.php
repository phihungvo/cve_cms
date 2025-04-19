<?php

namespace App\Domains\Report\ControllerApi;

use Illuminate\Http\Request;
use App\Domains\Report\Service\ControllerApi\DailyDistanceImpressionReach as DailyDistanceImpressionReachService;
use Illuminate\Http\JsonResponse;

class DailyDistanceImpressionReach
{
    protected $service;

    public function __construct(DailyDistanceImpressionReachService $service)
    {
        $this->service = $service;
    }

    /**
     * Xử lý API request và trả về dữ liệu DailyDistanceImpressionReach
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function data(Request $request): JsonResponse
    {
        try {
            // Gọi service với request và thông tin user đăng nhập
            $data = $this->service->data();

            // Trả về mảng trực tiếp, không bọc trong key "data"
            return response()->json($data, 200);
        } catch (\Exception $e) {
            // Xử lý lỗi và trả về response lỗi
            return response()->json([
                'message' => 'An error occurred while processing your request.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}