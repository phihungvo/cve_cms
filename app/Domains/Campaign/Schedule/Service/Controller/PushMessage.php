<?php

namespace App\Domains\Campaign\Schedule\Service\Controller;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\Playlist\Model\PlaylistModel;
use App\Services\Mqtt\MqttService;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;

class PushMessage
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
    public function pushMessage(): string
    {
        // Thực hiện logic gửi tin nhắn ở đây
        $scheduleId = $this->request->input('schedule_id');

        $data = $this->data($scheduleId);

        $devices = Schedule::find($scheduleId)->devices()->withPivot('id')->get();

        try {
            // kết nối mqtt broker
            $this->mqttService->connect();

            // publish message đến tất cả các topic
            foreach ($devices as $device) {
                $topic = 'device/'.$device->serial.'/fpp/schedule/add';
                $displayId = $device->pivot->id;
                $this->mqttService->publish($topic, json_encode(array_merge($data, ['display_id' => $displayId])), 1, false);
            }
        } finally {
            // Giữ kết nối 1s trước khi đóng để tránh mất dữ liệu.
            sleep(1);
            $this->mqttService->disconnect();
        }
        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }

    protected function data(int $scheduleId): array
    {
        // chuẩn bị dữ liệu để gửi theo cấu trúc sau.

        // {
        //  "playlist": "tao-moi-mot-playlist",
        //  "display_id" : '123456789',
        //  "day": 7,
        //  "startTime": "00:00:00",
        //  "endTime": "24:00:00",
        //  "startDate": "2025-03-19",
        //  "endDate": "2030-12-31"
        // }

        // Lấy schedule theo id
        $schedule = Schedule::with(PlaylistModel::TABLE)
            ->findOrFail($scheduleId);

        // chuyển bị dữ liệu theo mẫu trên
        $data = [
            'playlist' => $schedule->playlist->name,
            'display_id' => $schedule->devices->pluck('serial')->toArray(),
            'day' => '7',
            'startTime' => $schedule->start_time->format('H:i:s'),
            'endTime' => $schedule->end_time->format('H:i:s'),
            'startDate' => $schedule->start_time->format('Y-m-d'),
            'endDate' => $schedule->end_time->format('Y-m-d'),
        ];

        return $data;
    }
}
