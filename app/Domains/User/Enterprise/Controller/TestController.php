<?php

namespace App\Domains\User\Enterprise\Controller;

use App\Http\Controllers\Controller;
use App\Services\Mqtt\MqttService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PhpMqtt\Client\Exceptions\DataTransferException;
use PhpMqtt\Client\Exceptions\RepositoryException;

class TestController extends Controller
{
    protected MqttService $mqttService;

    public function __construct(MqttService $mqttService)
    {
        $this->mqttService = $mqttService;
    }

    public function index(Request $request): JsonResponse
    {
        $topic = $request['topic'] ?? 'laravel/test';
        $message = $request['message'] ?? 'topic1';

        try {
            $this->mqttService->publish($topic, $message, 2);

            return response()->json([
                'message' => $message,
                'topic' => $topic,
                'timestamp' => date(
                    'Y-m-d H:i:s'
                )]);
        } catch (DataTransferException|RepositoryException|\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        } finally {
            $this->mqttService->disconnect();
        }

    }
}
