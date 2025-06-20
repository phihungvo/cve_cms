<?php declare(strict_types=1);

namespace App\Domains\Device\Action;

use App\Services\Mqtt\MqttService;
use Exception;

class SendEventsAction extends ActionAbstract
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->sendEvents();
    }

    /**
     * @throws Exception
     */
    protected function sendEvents(): void
    {
        $mqttService = app(MqttService::class);
        $topic = 'camera/events';

        try {
            $mqttService->connect();
            if ($mqttService->connected) {
                foreach ($this->data as $event) {
                    $mqttService->publish($topic, json_encode($event));
                    sleep(5);
                }
                sleep(2);
                $mqttService->disconnect();

                return;
            }
            throw new Exception('MQTT server is not connected.');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
