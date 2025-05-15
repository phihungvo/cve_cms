<?php

namespace App\Domains\Notification\Controller;

use App\Domains\Core\Controller\ControllerAbstract;
use App\Domains\Notification\Model\DeviceNotification;
use Illuminate\Http\JsonResponse;

class DeviceNotificationStatusController extends ControllerAbstract
{
    public function show($notificationId): JsonResponse
    {
        try {
            $notification = DeviceNotification::where('notification_id', $notificationId)
                ->with('device')
                ->get();

            $totalSent = $notification->where('is_sent', true)->count();
            $totalRead = $notification->where('is_read', true)->count();
            $devices = $notification->map(function ($item) {
                return [
                    'device_id' => $item->device_id,
                    'device_name' => $item->device->name ?? 'Unknown',
                    'is_sent' => $item->is_sent,
                    'is_read' => $item->is_read,
                    'sent_at' => $item->sent_at?->toDateTimeString(),
                    'read_at' => $item->read_at?->toDateTimeString(),
                ];
            });

            return response()->json([
                'status' => 'success',
                'data' => [
                    'total_sent' => $totalSent,
                    'total_read' => $totalRead,
                    'devices' => $devices,
                ],
            ]);
        } catch (\Exception $e) {
            \Log::error('Failed to load device notification status: ' . $e->getMessage());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to load device notification status',
            ], 500);
        }
    }
}