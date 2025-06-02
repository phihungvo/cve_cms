<?php declare(strict_types=1);

namespace App\Domains\Playlist\Service\Controller;

use App\Domains\Playlist\Model\PlaylistModel as Model;
use App\Domains\Playlist\PlaylistGroup\Model\PlaylistGroupModel;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class IndexService extends ControllerAbstract
{
    // constructor
    public function __construct(protected Request $request, protected Authenticatable $auth)
    {
        $this->filters();
    }

    protected function filters(): void
    {

    }

    /**
     * @return mixed
     */
    public function data(): mixed
    {
        return $this->dataCore() + [
            // Trả về dữ liệu tùy chỉnh cho trang index
            'enterprises' => $this->enterprises(),
            'list' => $this->list(), // Trả về danh sách playlist
            'playlist_groups' => $this->playlistGroups(), // Trả về danh sách nhóm playlist
        ];
    }

    /**
     * Lấy danh sách Enterprise có playlist
     *
     * @return Collection
     */
    protected function enterprises(): Collection
    {
        return $this->cache(
            fn () => Enterprise::query()
                ->whereHas('playlists') // playlists() trong Enterprise model
                ->get()
        );
    }

    /**
     * Lấy danh sách playlist
     *
     * @return Collection
     */
    protected function list(): Collection
    {
        return Model::query()
            ->roleRoot() // PlaylistBuilder->roleRoot()
            ->roleOwner()
            ->userHasPerrmission('access-playlist-list')
            ->filterByEnterpriseId((int)$this->request->input('enterprise_id'))
            ->filterByPlaylistGroup((int)$this->request->input('playlist_group_id'))
            ->getEnterpriseName()
            ->getPublishedDisplaysCount()
            ->get();
    }

    protected function playlistGroups(): Collection
    {
        return PlaylistGroupModel::query()
            ->whereByEnterprise()
            ->filterByEnterpriseId((int)$this->request->input('enterprise_id'))
            ->listSimple()
            ->get();
    }
}
