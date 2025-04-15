<?php

namespace App\Domains\Playlist\Action;

use App\Domains\Playlist\Model\PlaylistModel;
use App\Services\Mqtt\MqttService;

abstract class PushMessageAbstract extends ActionAbstract
{
    protected ?PlaylistModel $playlist;

    protected MqttService $mqttService;

    public function handle(): string
    {
        $this->playlist = $this->getPlaylist();
        $this->mqttService = app(MqttService::class);

        return $this->pushMessage();
    }

    abstract protected function pushMessage(): string|false;

    protected function getPlaylist()
    {
        $playlistId = $this->request->get('playlist_id');

        return PlaylistModel::with('medias', 'displays')
            ->find($playlistId);

    }

    protected function data(): array
    {
        return [
            'display_id' => 'dummy display_id',
            'name' => $this->name(),
            'video' => $this->video(),
            'src' => $this->src(),
        ];
    }

    protected function name(): string
    {
        return $this->playlist->name ?? '';
    }

    protected function video(): array
    {
        return $this->playlist->medias()->get()->map(function ($media) {
            $path = parse_url($media->media_url, PHP_URL_PATH);

            return urldecode(basename($path)); // giữ nguyên tiếng Việt có dấu
        })->toArray();
    }

    protected function src()
    {
        return $this->playlist->medias()->get()->map(function ($media) {
            $patternBucket = '/https?:\/\/[^\/]+\/([^\/]+)/u';
            $patternObject = '/https?:\/\/[^\/]+\/[^\/]+\/(.+)/u';

            $bucketName = preg_match($patternBucket, $media->media_url, $matches) ? $matches[1] : '';
            $objectName = preg_match($patternObject, $media->media_url, $matches) ? $matches[1] : '';

            return [
                'bucketName' => urldecode($bucketName),
                'objectName' => urldecode($objectName),
            ];
        })->toArray();
    }
}
