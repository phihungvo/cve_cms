<?php declare(strict_types=1);

namespace App\Domains\Playlist\Action;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\Playlist\Model\PlaylistModel;
use Illuminate\Database\QueryException;
use PDOException;
use RuntimeException;
use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

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
                $this->row->update($this->data);

                // Đồng bộ hóa media với position
                $mediaData = [];
                foreach ($this->data['medias'] as $media) {
                    $mediaData[$media['id']] = ['position' => $media['position']];
                }
                $this->row->medias()->sync($mediaData);

                // Kiểm tra playlist có thuộc schedule nào chưa.
                $schedule = Schedule::query()
                    ->where('playlist_id', $this->row->id)
                    ->first();

                // Đồng bộ thiết bị với thông tin pivot
                $this->row->devices()->syncWithPivotValues(
                    $this->data['deviceIds'] ?? [],
                    [
                        PlaylistModel::FOREIGN => $this->row->id,
                        'schedule_id' => $schedule->id ?? null,
                    ]
                );

                // Đồng bộ các nhóm playlist (playlist_group)
                $this->row->playlistGroups()->sync($this->data['playlist_groups']);
            });
        } catch (PDOException|QueryException $e) {
            throw new RuntimeException(
                __('playlist-update.error.database', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ModelNotFoundException $e) {
            throw new RuntimeException(
                __('playlist-update.error.not-found', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (ValidationException $e) {
            throw new RuntimeException(
                __('playlist-update.validation-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        } catch (Throwable $e) {
            throw new RuntimeException(
                __('playlist-update.unknown-error', ['message' => $e->getMessage()]),
                0,
                $e
            );
        }
    }
}

