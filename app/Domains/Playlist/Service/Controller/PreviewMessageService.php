<?php

namespace App\Domains\Playlist\Service\Controller;

use App\Domains\Playlist\Model\PlaylistModel as Model;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\Request;

class PreviewMessageService extends ControllerAbstract
{
    protected ?Model $row;

    public function __construct(Request $request, Authenticatable $auth, Model $row)
    {
        $this->row = $row;
    }

    public function data(): mixed
    {
        return [
            'display_id' => $this->displayId(),
            'name' => $this->name(),
            'video' => $this->video(),
            'src' => $this->src(),
        ];
    }

    protected function displayId(): array
    {
        return $this->row->devices->pluck('serial')->toArray();
    }

    protected function name(): string
    {
        return $this->row->name ?? '';
    }

    protected function video(): array
    {
        return $this->row->medias()->get()->map(function ($media) {
            $path = parse_url($media->media_url, PHP_URL_PATH);

            return urldecode(basename($path)); // giữ nguyên tiếng Việt có dấu
        })->toArray();
    }

    protected function src(): array
    {
        return $this->row->medias()->get()->map(function ($media) {
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
