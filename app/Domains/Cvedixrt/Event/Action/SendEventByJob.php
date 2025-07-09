<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Action;

use App\Domains\Cvedixrt\Event\Model\CvedixrtEventModel;
use App\Services\Mqtt\MqttService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\Log;

class SendEventByJob extends ActionAbstract
{
    /**
     * @throws Exception
     */
    public function handle(): void
    {
        $this->senEventByJob();
    }

    /**
     * @throws Exception
     */
    protected function senEventByJob(): void
    {
        $mqtt = app(MqttService::class);
        $this->row = CvedixrtEventModel::query()
            ->with(['instanceRule' => function ($query) {
                $query->select('priority');
            }])
            ->where('id', $this->data['id'])
            ->first();

        $data = $this->prepareData();

        try {
            $mqtt->connect();

            // Nên kiểm tra bằng method nếu có, thay vì thuộc tính
            if (method_exists($mqtt, 'isConnected') ? $mqtt->isConnected() : $mqtt->connected) {
                usleep($this->row->delay_time_ms ?? 0);
                $mqtt->publish('camera/events', json_encode($data), 1);
                $mqtt->disconnect();
            } else {
                Log::error('MQTT server is not connected.');
                throw new Exception('MQTT server is not connected.');
            }
        } catch (Exception $e) {
            Log::error('MQTT server is not connected.');
            throw new Exception('MQTT server is not connected.');
        }
    }

    /**
     * @return array
     */
    public function prepareData(): array
    {
        return [
            'id' => $this->row->id,
            'uuid' => $this->row->uuid,
            'image_url' => $this->row->image_url,
            'video_url' => $this->row->video_url,
            'detected_object' => $this->row->detected_object,
            'event_name' => $this->row->event_name,
            'event_value' => $this->row->event_value,
            'event_type' => $this->row->event_type,
            'instance_rule_id' => $this->row->instance_rule_id,
            'priority' => $this->row->instanceRule->priority ?? 1,
            'created_at' => Carbon::parse($this->row->created_at)->setTimezone('Asia/Ho_Chi_Minh')->toDateTimeString(),
        ];
    }
}
