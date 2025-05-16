<?php declare(strict_types=1);

namespace App\Domains\Playlist\Model\Builder;

use App\Domains\CoreApp\Model\Builder\BuilderAbstract;
use App\Domains\Display\Model\Display;

class PlaylistBuilder extends BuilderAbstract
{
    # Khởi tạo các phương thức tùy chỉnh cho Eloquent Builder

    public function roleRoot()
    {
        if (auth()->user()?->isRoleRoot()) {
            return $this->withTrashed();
        }

        return $this;
    }

    public function roleOwner()
    {
        if (auth()->user()?->isOwner()) {
            return $this->where('enterprise_id', auth()->user()->enterprise_id);
        }

        return $this;
    }

    public function withEnterprise(): self
    {
        return $this->with('enterprises');
    }

    /**
     * Lấy thuộc tính name trong bảng Enterprise.
     *
     * @return self
     */
    public function getEnterpriseName(): self
    {
        return $this->with(['enterprise' => function ($query) {
            $query->select('id', 'name');
        }]);
    }

    /**
     * Đếm displays mà trạng thái playlist_published != 0 trong playlist.
     *
     * @return PlaylistBuilder
     */
    public function getPublishedDisplaysCount(): self
    {
        return $this->withCount(['displays' => function ($query) {
            $query->where(Display::PLAYLIST_PUBLISHED, '!=', 0);
        }]);
    }

    /**
     * Đếm tất cả display đang có trong playlist.
     *
     * @return PlaylistBuilder
     */
    public function getDisplaysCount(): self
    {
        return $this->withCount(['displays' => function ($query) {
            $query->where(Display::PLAYLIST_PUBLISHED, '!=', 0);
        }]);
    }

    public function filterByEnterpriseId(?int $enterpriseId): self
    {
        if ($enterpriseId) {
            $this->where('enterprise_id', $enterpriseId);
        }

        return $this;
    }

    public function filterByPlaylistGroup(?int $playlistGroupId): self
    {
        if ($playlistGroupId) {
            $this->whereHas('playlistGroups', function ($q) use ($playlistGroupId) {
                $q->where('playlist_group_id', $playlistGroupId);
            });
        }

        return $this;
    }

    public function userHasPerrmission($alias): PlaylistBuilder|\Illuminate\Support\Collection
    {
        if (auth()->user()->hasPermission($alias)) {
            return $this->where('enterprise_id', auth()->user()->enterprise_id);
        }
        return $this->where('enterprise_id', 0);
    }
}
