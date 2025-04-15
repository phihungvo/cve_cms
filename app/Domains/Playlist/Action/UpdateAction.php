<?php

namespace App\Domains\Playlist\Action;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\Playlist\Model\PlaylistModel;

class UpdateAction extends CreateUpdateAbstract
{
    /**
     * Update Playlist and Medias
     *
     * @return void
     *
     * @override
     */
    protected function save(): void
    {
        try {
            $this->transaction(function () {
                // Cập nhật thông tin playlist
                $this->row->name = $this->data['name'];
                $this->row->description = $this->data['description'];

                // Kiểm tra playlist có thuộc schedule nào chưa.
                $schedule = Schedule::query()
                    ->where('playlist_id', $this->row->id)
                    ->first();

                // Logic cập nhật media
                if (!empty($this->data['medias']) && is_array($this->data['medias'])) {
                    // Chuyển đổi mảng medias thành định dạng [media_id => ['position' => value]]
                    $mediaData = [];
                    foreach ($this->data['medias'] as $media) {
                        // Kiểm tra id và position hợp lệ
                        if (!empty($media['id']) && isset($media['position'])) {
                            $mediaData[$media['id']] = ['position' => (int)$media['position']];
                        }
                    }

                    // Đồng bộ media với position
                    $this->row->medias()->sync($mediaData);
                    $this->row->devices()->syncWithPivotValues(
                        $this->data['deviceIds'] ?? [],
                        [
                            PlaylistModel::FOREIGN => $this->row->id,
                            'schedule_id' => $schedule->id ?? null,
                        ]
                    );
                } else {
                    // Nếu không có media, xóa hết các liên kết trong bảng trung gian
                    $this->row->medias()->sync([]);
                    $this->row->devices()->syncWithPivotValues(
                        $this->data['deviceIds'] ?? [],
                    );
                }

                // Lưu thay đổi vào database
                $this->row->save();
            });
        } catch (\Throwable $e) {
            logger()->error('Lỗi khi cập nhật Playlist and Media: '.$e);
        }
    }
}
