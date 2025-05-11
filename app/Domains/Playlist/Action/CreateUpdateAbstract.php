<?php declare(strict_types=1);

namespace App\Domains\Playlist\Action;

use App\Domains\Playlist\Model\PlaylistModel as Model;

abstract class CreateUpdateAbstract extends ActionAbstract
{
    abstract protected function save();

    public function handle(): Model
    {
        $this->data();
        $this->check();
        $this->save();

        return $this->row;
    }

    private function data(): void
    {
        $this->dataName();
        $this->dataDescription();
        $this->dataMedias();
        $this->dataPlaylistGroups();
    }

    private function check(): void
    {

    }

    private function dataName(): void
    {
        $this->data['name'] = trim($this->data['name']);
    }

    private function dataDescription(): void
    {
        $this->data['description'] = trim($this->data['description']);
    }

    protected function dataPlaylistGroups(): void
    {
        $this->data['playlist_groups'] ??= [];
    }

    protected function dataMedias(): void
    {
        $medias = $this->data['medias'] ?? [];
        $formattedMedias = [];
        foreach ($medias as $media) {
            if (!empty($media['id']) && isset($media['position'])) {
                $formattedMedias[] = [
                    'id' => (int) $media['id'],
                    'position' => (int) $media['position'],
                ];
            }
        }
        $this->data['medias'] = $formattedMedias;
    }
}
