<?php

namespace App\Domains\Playlist\Action;

use Exception;

class PushMessageAction extends PushMessageAbstract
{
    /**
     * @throws Exception
     *
     * @override
     */
    protected function pushMessage(): string|false
    {

        $data = $this->data();
        // lấy ra tất cả device dưa vào playlist - schedule n---display---n device
        $devices = $this->playlist->displays->map(function ($display) {
            return [
                'display_id' => $display->id,
                'device_serial' => $display->device->serial,
            ];
        });

        try {
            $this->mqttService->connect();

            //        lặp qua tất cả devices sau đó gửi message
            foreach ($devices as $device) {
                $topic = 'device/'.$device['device_serial'].'/fpp/playlist/add';
                $message = json_encode(array_merge($data, ['display_id' => $device['display_id']]), JSON_UNESCAPED_UNICODE);
                $this->mqttService->publish($topic, $message, 1, false);
            }
        } finally {
            // Giữ kết nối 1s trước khi đóng để tránh mất dữ liệu.
            sleep(1);
            $this->mqttService->disconnect();
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE);
    }
}
