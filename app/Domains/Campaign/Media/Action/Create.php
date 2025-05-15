<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Media\Action;

use App\Domains\Campaign\Media\Model\Media;
use Illuminate\Support\Facades\Log;

class Create
{
    protected array $data;

    public function handle(array $data): Media
    {
        try {
            return Media::create($data);
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Failed to create media record', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    protected function createMedia(): Media
    {
        Log::info('Processing media with data: ', $this->data);

        try {
            // Kiểm tra xem media đã tồn tại dựa trên media_url
            $existingMedia = Media::where('media_url', $this->data['media_url'])->first();

            if ($existingMedia) {
                throw new \Exception(__('media-create.media-exists', ['name' => $this->data['file_name'] ?? $this->data['name']]));
            }

            // Nếu chưa tồn tại, tạo mới
            $media = Media::create($this->data);

            if ($media) {
                Log::info('Media created successfully: ', $media->toArray());
            } else {
                Log::error('Failed to create media: ', $this->data);
                throw new \Exception('Failed to create media record in database.');
            }

            return $media;
        } catch (\Exception $e) {
            Log::error('Error processing media: ', ['error' => $e->getMessage()]);
            throw $e;
        }
    }
}