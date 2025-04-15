<?php
namespace App\Domains\Position\Fractal;

use League\Fractal\TransformerAbstract;

class PositionTransformer extends TransformerAbstract
{
    public function transform($position): array
    {
        // Xử lý cả instance Position hoặc mảng
        $deviceId = $position['device_id'] ?? $position->device_id;
        $latitude = isset($position['latitude']) ? (float) $position['latitude'] : (float) $position->latitude;
        $longitude = isset($position['longitude']) ? (float) $position['longitude'] : (float) $position->longitude;
        $timestamp = isset($position['date_at']) ? $position['date_at'] : $position->date_at->toDateTimeString();
        $device = isset($position['device']) ? (is_array($position['device']) ? $position['device'] : $position['device']->toArray()) : ($position->device ? $position->device->toArray() : []);

        return [
            'device_id' => $deviceId,
            'latitude' => $latitude,
            'longitude' => $longitude,
            'timestamp' => $timestamp,
            'device' => $device,
        ];
    }
}