<?php
declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Controller;

use App\Domains\Cvedixrt\Instance\Service\Controller\RTAnalyticsService as ControllerService;
use App\Exceptions\NotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CanvasController extends ControllerAbstract
{
    /**
     * Lấy danh sách shapes (lines và zones) của một instance
     *
     * @param int $instanceId
     *
     * @return JsonResponse
     */
    public function getShapes(int $instanceId): JsonResponse
    {
        try {
            $this->row($instanceId);

            return response()->json([
                'success' => true,
                'data' => [
                    'lines' => $this->row->lines ?? [],
                    'zones' => $this->row->zones ?? [],
                ],
            ]);
        } catch (NotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Instance not found',
            ], 404);
        }
    }

    /**
     * Lưu shapes (lines và zones) cho một instance
     *
     * @param Request $request
     * @param int $instanceId
     *
     * @return JsonResponse
     */
    public function saveShapes(Request $request, int $instanceId): JsonResponse
    {
        try {
            $instance = $this->row($instanceId);

            $shapes = $request->input('shapes', []);

            $processedShapes = ControllerService::new($this->request, $this->auth, $this->row)->processShapesData($shapes);

            $currentLines = $instance->lines ?? [];
            $currentZones = $instance->zones ?? [];

            $updateData = [];
            if (isset($processedShapes['lines'])) {
                $updateData['lines'] = $processedShapes['lines'];
            } else {
                $updateData['lines'] = $currentLines;
            }
            if (isset($processedShapes['zones'])) {
                $updateData['zones'] = $processedShapes['zones'];
            } else {
                $updateData['zones'] = $currentZones;
            }

            $instance->update($updateData);

            return response()->json([
                'success' => true,
                'message' => __('Shapes saved successfully'),
            ]);
        } catch (NotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Instance not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Đã xảy ra lỗi: '.$e->getMessage(),
            ], 400);
        }
    }
}
