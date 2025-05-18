<?php

namespace App\Domains\PlaylistGroup\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Playlist\Model\PlaylistModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlaylistGroupMap extends ModelAbstract
{
    protected $table = 'playlist_group_map';

    public const TABLE = 'playlist_group_map';

    public const PRIMARY = 'id';

    public const FOREIGN = 'playlist_group_map_id';

    protected $fillable = [
        'playlist_id',
        'playlist_group_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(PlaylistModel::class);
    }

    public function playlistGroup(): BelongsTo
    {
        return $this->belongsTo(PlaylistGroupModel::class, 'playlist_group_id');
    }
}
