<?php
declare(strict_types=1);

namespace App\Domains\Playlist\Model;

use App\Domains\Campaign\Media\Model\Media;
use App\Domains\CoreApp\Model\ModelAbstract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $playlist_id
 * @property int $media_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property int $position
 * @property-read \App\Domains\Campaign\Media\Model\Media $media
 * @property-read \App\Domains\Playlist\Model\PlaylistModel $playlist
 *
 * @method static Builder<static>|PlaylistMediaModel byEnterprise()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistMediaModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistMediaModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistMediaModel onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistMediaModel query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistMediaModel whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistMediaModel whereMediaId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistMediaModel wherePlaylistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistMediaModel wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistMediaModel whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistMediaModel withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PlaylistMediaModel withoutTrashed()
 *
 * @mixin \Eloquent
 */
class PlaylistMediaModel extends ModelAbstract
{
    use SoftDeletes;

    protected $table = 'playlist_media';
    public const TABLE = 'playlist_media';

    public $timestamps = true;

    protected $fillable = [
        'playlist_id',
        'media_id',
        'position',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    public function playlist(): BelongsTo
    {
        return $this->belongsTo(PlaylistModel::class, 'playlist_id', 'id');
    }

    public function media(): BelongsTo
    {
        return $this->belongsTo(Media::class, 'media_id', 'id');
    }
}