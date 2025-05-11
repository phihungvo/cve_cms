<?php

namespace App\Domains\Playlist\Action;

use App\Domains\Playlist\Model\PlaylistModel;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Validation\ValidationException;
use PDOException;
use RuntimeException;
use Throwable;

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

                // Đồng bộ các nhóm playlist (playlist_group)
                $this->row->playlistGroups()->sync($this->data['playlist_groups']);
            });
        } catch (PDOException|QueryException $e) {
            throw new RuntimeException(
                __('playlist-create.error.database', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ModelNotFoundException $e) {
            throw new RuntimeException(
                __('playlist-create.error.not-found', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ValidationException $e) {
            throw new RuntimeException(
                __('playlist-create.validation-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new RuntimeException(
                __('playlist-create.unknown-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}

