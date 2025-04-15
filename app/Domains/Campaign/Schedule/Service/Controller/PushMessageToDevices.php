<?php

namespace App\Domains\Campaign\Schedule\Service\Controller;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\Device\Model\Device;
use App\Services\Mqtt\MqttService;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;

class PushMessageToDevices
{
    protected Request $request;

    protected ?Authenticatable $auth;

    protected MqttService $mqttService;

    public function __construct(Request $request, $auth, MqttService $mqttService)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->mqttService = $mqttService;
    }

    public static function new(Request $request, $auth, MqttService $mqttService): self
    {
        return new self($request, $auth, $mqttService);
    }

    /**
     * @throws \Exception
     */
    public function pushMessageToDevices(): string
    {
        // Thực hiện logic gửi tin nhắn ở đây
        $scheduleId = $this->request->get('schedule_id');

        $schedule = Schedule::findOrFail($scheduleId);

        // Kiểm tra xem schedule có tồn tại hay không
        if (!$schedule) {
            throw new \Exception('Schedule not found');
        }

        $data = $this->data($schedule);

        $device_ids = $this->request->get('device_ids');

        // lấy danh sách device với mảng device_ids
        $devices = Device::query()
            ->whereIn('id', $device_ids)
            ->with('displays', function ($query) use ($schedule) {
                return $query->where('playlist_id', $schedule->playlist_id);
            })
            ->get();

        try {
            // kết nối mqtt broker
            $this->mqttService->connect();

            // publish message đến tất cả các topic
            foreach ($devices as $device) {
                $topic = 'device/'.$device['serial'].'/fpp/schedule/add';
                $displayId = $device->displays()->first()->id;
                $this->mqttService->publish(
                    $topic,
                    json_encode(array_merge($data, ['display_id' => $displayId]), JSON_UNESCAPED_UNICODE),
                    1,
                    false
                );
            }
        } finally {
            sleep(1);
            $this->mqttService->disconnect();
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    protected function data(Schedule $schedule): array
    {
        // chuẩn bị dữ liệu
        $data = [
            'playlist' => $schedule->playlist->name,
            'display_id' => $schedule->devices()->pluck('serial')->toArray(),
            'day' => '7',
            'startTime' => $schedule->start_time->format('H:i:s'),
            'endTime' => $schedule->end_time->format('H:i:s'),
            'startDate' => $schedule->start_time->format('Y-m-d'),
            'endDate' => $schedule->end_time->format('Y-m-d'),

        ];

        return $data;
    }
}
