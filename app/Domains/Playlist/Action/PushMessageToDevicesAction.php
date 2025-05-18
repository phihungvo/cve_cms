<?php

namespace App\Domains\Playlist\Action;

class PushMessageToDevicesAction extends PushMessageAbstract
{
    protected function pushMessage(): array
    {
        $data = $this->data();

        $device_ids = $this->request->input('device_ids');
        $devices = $this->playlist->displays->whereIn('device_id', $device_ids)->map(function ($display) {
            return [
                'display_id' => $display->id,
                'device_serial' => $display->device->serial,
            ];
        });

        try {
            $this->mqttService->connect();

            foreach ($devices as $device) {
                $topic = 'device/'.$device['device_serial'].'/fpp/playlist/add';
                $message = json_encode(array_merge($data, ['display_id' => $device['display_id']]), JSON_UNESCAPED_UNICODE);
                $this->mqttService->publish($topic, $message, 1, false);
            }
        } finally {
            sleep(1);
            $this->mqttService->disconnect();
        }

        return $data;
    }
}
