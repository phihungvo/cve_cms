<?php

namespace App\Services\Mqtt;

use Exception;
use PhpMqtt\Client\ConnectionSettings;
use PhpMqtt\Client\Exceptions\ConfigurationInvalidException;
use PhpMqtt\Client\Exceptions\ConnectingToBrokerFailedException;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\ProtocolNotSupportedException;
use PhpMqtt\Client\MqttClient;

class MqttService
{
    protected MqttClient $mqtt;

    protected mixed $server;

    protected mixed $port;

    protected string $clientId;

    protected ConnectionSettings $connectionSettings;

    protected bool $connected = false; // Biến kiểm tra kết nối.

    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->server = env('MQTT_SERVER', 'broker.emqx.io');
        $this->port = env('MQTT_PORT', 1883);
        $this->clientId = 'cvedix-publisher-'.uniqid();

        $this->connectionSettings = (new ConnectionSettings())
            ->setUseTls($this->port == 8883) // Sử dụng TLS nếu port là 8883
            ->setTlsVerifyPeer(false)
            ->setTlsVerifyPeerName(false);

        // Không kết nối ngay lập tức
        $this->mqtt = new MqttClient($this->server, $this->port, $this->clientId);

        //        try {
        //            $this->mqtt = new MqttClient($this->server, $this->port, $this->clientId);
        //            $this->mqtt->connect($this->connectionSettings);
        //        } catch (ProtocolNotSupportedException|ConfigurationInvalidException|ConnectingToBrokerFailedException $e) {
        //            throw new Exception('MQTT connection failed: '.$e->getMessage());
        //        }
    }

    public function connect(): void
    {
        if (!$this->connected) {
            try {
                $this->mqtt->connect($this->connectionSettings);
                $this->connected = true; // Đánh dấu là đã kết nối

            } catch (ProtocolNotSupportedException|ConfigurationInvalidException|ConnectingToBrokerFailedException $e) {
                throw new Exception('MQTT connection failed: '.$e->getMessage());
            }
        }
    }

    /**
     * Publish một tin nhắn lên MQTT với topic và message đã chỉ định.
     *
     * @param string $topic
     * @param mixed $message
     * @param int $qos Quality of Service level for message delivery (0, 1, or 2). Default is 0.
     * @param bool $retain Whether the message should be retained by the broker. Default is false.
     *
     * @throws Exception
     */
    public function publish(string $topic, mixed $message, int $qos = 0, bool $retain = false): void
    {
        try {
            $this->mqtt->publish($topic, $message, $qos, $retain);
            usleep(100000); // thêm delay để tránh quá tải.
        } catch (Exception $e) {
            throw new Exception('MQTT publish failed: '.$e->getMessage());
        }
    }

    /**
     * Đóng kết nối MQTT
     *
     * @throws Exception
     */
    public function disconnect(): void
    {
        try {
            $this->mqtt->disconnect();
        } catch (DataTransferException $e) {
            // Handle disconnection error
            throw new Exception('MQTT disconnection failed: '.$e->getMessage());
        }
    }
}
// Lệnh cài đặt mqtt.
// compose require php-mqtt/client
