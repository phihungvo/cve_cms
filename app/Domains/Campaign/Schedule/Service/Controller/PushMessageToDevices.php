<?php declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Service\Controller;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\Device\Model\Device;
use App\Services\Mqtt\MqttService;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Authenticatable;

class PushMessageToDevices
{
    protected Request $request;

    protected ?Authenticatable $auth;

    protected Schedule $row;

    protected MqttService $mqttService;

    public function __construct(Request $request, $auth, Schedule $row, MqttService $mqttService)
    {
        $this->request = $request;
        $this->auth = $auth;
        $this->row = $row;
        $this->mqttService = $mqttService;
    }

    public static function new(Request $request, $auth, Schedule $row, MqttService $mqttService): self
    {
        return new self($request, $auth, $row, $mqttService);
    }

    /**
     * @throws Exception
     */
    public function pushMessageToDevices(): array
    {
        $schedule =  $this->row;

        $device_ids = $this->request->get('device_ids');

        $data = $this->data($this->row, $device_ids);

        // lấy danh sách device với mảng device_ids
        $devices = Device::query()
            ->whereIn('id', $device_ids)
            ->with('displays', function ($query) use ($schedule) {
                return $query->where('playlist_id', $schedule->playlist_id)
                             ->where('playlist_published', 1);
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

        return $data;
    }

    protected function data(Schedule $schedule, array $device_ids): array
    {
        return [
           'playlist' => $this->getPlaylistName($schedule),
            'display_id' => $this->getDisplayIds($schedule, $device_ids ),
            'day' => '7',
            'startTime' => $this->formatData('time', $schedule->start_time),
            'endTime' => $this->formatData('time', $schedule->end_time),
            'startDate' => $this->formatData('date', $schedule->start_time),
            'endDate' => $this->formatData('date', $schedule->end_time),
        ];
    }

    protected function getPlaylistName(Schedule $schedule): string
    {
        return $schedule->playlist->name;
    }

    protected function getDisplayIds(Schedule $schedule, array $device_ids): array
    {
        return $schedule->devices()
            ->whereIn('device.id', $device_ids)
            ->where('playlist_published', 1)->pluck('serial')->toArray();
    }

    protected function formatData(string $type, $value): string
    {
        $strategies = [
            'time' => fn($time) => $time->format('H:i:s'),
            'date' => fn($time) => $time->format('Y-m-d'),
        ];

        return $strategies[$type]($value);
    }
}
