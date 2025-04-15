<?php

namespace App\Domains\Playlist\Action;

use App\Domains\Playlist\Model\PlaylistModel;

class CreateAction extends CreateUpdateAbstract
{
    /**
     * Create Playlist and Media
     *
     * @return void
     *
     * @override
     */
    protected function save(): void
    {
        try {
            $this->transaction(function () {
                // Tạo playlist
                $this->row = PlaylistModel::query()->create([
                    'name' => $this->data['name'],
                    'description' => $this->data['description'],
                    'enterprise_id' => $this->data['enterprise_id'] ?? null,
                ]);

                // Nếu có media, gắn vào bảng trung gian playlist_media
                if (!empty($this->data['medias']) && is_array($this->data['medias'])) {
                    $mediaData = [];
                    foreach ($this->data['medias'] as $media) {
                        if (isset($media['id']) && isset($media['position'])) {
                            $mediaData[$media['id']] = ['position' => $media['position']];
                        }
                    }
                    $this->row->medias()->sync($mediaData);
                    $this->row->devices()->syncWithPivotValues(
                        $this->data['deviceIds'] ?? [],
                        [PlaylistModel::FOREIGN => $this->row->id]
                    );
                }
            });
        } catch (\Throwable $e) {
            logger()->error('Lỗi khi tạo Playlist and Media: '.$e);
        }

    }
}
