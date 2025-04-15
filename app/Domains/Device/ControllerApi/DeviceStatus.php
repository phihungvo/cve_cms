<?php declare(strict_types=1);

namespace App\Domains\Device\ControllerApi;

use Illuminate\Http\JsonResponse;
use App\Domains\Device\Model\DeviceStatus as Model;
use App\Domains\Device\Service\ControllerApi\DeviceStatus as ControllerService;
use Illuminate\Support\Facades\Log;

class DeviceStatus extends ControllerApiAbstract
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        try {
            Log::info('========<<<<<<Info creating/updating device status');

            return $this->json($this->execute());
        } catch (\Exception $e) {
            Log::error('Error creating/updating device status: ' . $e->getMessage());

            return response()->json(['error' => 'Failed to create/update device status: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @return \App\Domains\Device\Model\DeviceStatus|array
     */
    protected function execute(): Model|array
    {
        $data = $this->data();
        Log::info('============> Serial called from device status: ' . gettype($data));

        // Kiểm tra xem Device có tồn tại không
        $device = \App\Domains\Device\Model\Device::where('serial', $data['serial'])
            ->byUserOrManager($this->auth)
            ->first();

        if (!$device) {
            throw new \Exception('Device with serial ' . $data['serial'] . ' not found');
        }

        // Kiểm tra xem DeviceStatus đã tồn tại chưa
        $deviceStatus = Model::where('serial', $data['serial'])->first();

        if ($deviceStatus) {
            // Nếu đã tồn tại, cập nhật
            $deviceStatus->update([
                'data' => $data['data'] ?? $deviceStatus->data,
                // 'error' => $data['error'] ?? $deviceStatus->error,
            ]);
        } else {
            throw new \Exception('Device status for serial ' . $data['serial'] . ' not found');
        }

        // Ghi log sự kiện vào DeviceLog
        \App\Domains\Device\Model\DeviceLog::logEvent(
            device: $device,
            deviceStatus: $deviceStatus,
            type: 'device_status_updated',
            description: 'Device status updated for serial: ' . $data['serial']
        );

        return $deviceStatus;
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
