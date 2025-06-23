<?php declare(strict_types=1);

namespace App\Domains\Report\Service\ControllerApi;

use Illuminate\Http\Request;
use App\Domains\Monitor\Service\System\Cpu;
use App\Domains\Monitor\Service\System\Disk;
use App\Domains\Monitor\Service\System\Memory;
use App\Domains\Monitor\Service\System\Summary;

class HealthSystem
{
    /**
     * @param \Illuminate\Http\Request $request
     *
     * @return self
     */
    public function __construct(protected Request $request)
    {
    }

    /**
     * @return array
     */
    public function data(): array
    {
        $systemData = [
            'summary' => $this->summary(),
            'cpu' => $this->cpu(),
            'memory' => $this->memory(),
            'disk' => $this->disk(),
        ];

        $healthStatus = $this->checkSystemHealth($systemData);

        return [
            'status' => $healthStatus['status'],
            'message' => $healthStatus['message'],
            'data' => $systemData,
        ];
    }

    /**
     * @param array $systemData
     *
     * @return array
     */
    protected function checkSystemHealth(array $systemData): array
    {
        $isHealthy = true;
        $messages = [];

        // Kiểm tra CPU
        if (isset($systemData['cpu']['usage']) && $systemData['cpu']['usage'] > 90) {
            $isHealthy = false;
            $messages[] = 'CPU usage exceeds 90%';
        }

        // Kiểm tra Memory
        if (isset($systemData['memory']['used_percent']) && $systemData['memory']['used_percent'] > 90) {
            $isHealthy = false;
            $messages[] = 'Memory usage exceeds 90%';
        }

        // Kiểm tra Disk
        if (isset($systemData['disk']['used_percent']) && $systemData['disk']['used_percent'] > 95) {
            $isHealthy = false;
            $messages[] = 'Disk usage exceeds 95%';
        }

        return [
            'status' => $isHealthy ? 200 : 503,
            'message' => $isHealthy ? 'System is healthy' : implode('; ', $messages),
        ];
    }

    /**
     * @return ?array
     */
    protected function summary(): ?array
    {
        return Summary::new()->get();
    }

    /**
     * @return ?array
     */
    protected function cpu(): ?array
    {
        return Cpu::new()->get();
    }

    /**
     * @return ?array
     */
    protected function memory(): ?array
    {
        return Memory::new()->get();
    }

    /**
     * @return ?array
     */
    protected function disk(): ?array
    {
        return Disk::new()->get();
    }
}