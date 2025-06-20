<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Action;

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
        $mqttServer = app(MqttService::class);
        $topic = 'camera/events';

        try {
            $mqttServer->connect();
            if ($mqttServer->connected) {
                foreach ($this->data as $event) {
                    $mqttServer->publish($topic, json_encode($event), 1);
                    sleep(5);
                }
                sleep(2);
                $mqttServer->disconnect();

                return;
            }
            throw new Exception('MQTT server is not connected.');
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
    }
}
