<?php declare(strict_types=1);

namespace App\Domains\Device\ControllerApi;

use Illuminate\Http\JsonResponse;
use App\Domains\Device\Model\DeviceLog as DeviceLogModel;
use App\Domains\Device\Service\ControllerApi\DeviceLog as ControllerService;
use Illuminate\Support\Facades\Log;

class DeviceLog extends ControllerApiAbstract
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function __invoke(): JsonResponse
    {
        try {
            $deviceLog = $this->execute();
            return response()->json($deviceLog->toArray());
        } catch (\Exception $e) {
            Log::error('Error creating device log: ' . $e->getMessage());
            return response()->json(['error' => 'Failed to create device log: ' . $e->getMessage()], 500);
        }
    }

    /**
     * @return \App\Domains\Device\Model\DeviceLog
     */
    protected function execute(): DeviceLogModel
    {

        $data = $this->data();
        // Kiểm tra xem Device có tồn tại không
        $device = \App\Domains\Device\Model\Device::where('serial', $data['serial'])
            ->byUserOrManager($this->auth)
            ->first();

        if (!$device) {
            throw new \Exception('Device with serial ' . $data['serial'] . ' not found');
        }

        return DeviceLogModel::create([
            'serial' => $data['serial'],
            'type' => $data['type'] ?? 'default_type',
            'description' => $data['description'] ?? null,
            // 'data' => $data['data'] ?? null, // Mảng sẽ được tự động mã hóa thành JSON
            // 'status' => $data['status'] ?? null,
            // 'error' => $data['error'] ?? null,
        ]);
    }

    /**
     * @return array
     */
    protected function data(): array
    {
        return ControllerService::new($this->request, $this->auth)->data();
    }
}
