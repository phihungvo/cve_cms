<?php declare(strict_types=1);

namespace App\Domains\PlaylistGroup\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\PlaylistGroup\Model\Builder\PlaylistGroupBuilder;
use App\Domains\PlaylistGroup\Model\Collection\PlaylistGroupCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class PlaylistGroupModel extends ModelAbstract
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @const string
     */
    const PRIMARY = 'id';

    /**
     * @var string
     */
    protected $table = 'playlist_group';

    /**
     * @const string
     */
    public const TABLE = 'playlist_group';

    /**
     * @const string
     */
    public const FOREIGN = 'playlist_group_id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'enterprise_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $dates = ['deleted_at'];

    /**
     * Create a custom collection instance.
     *
     * @param array $models
     *
     * @return PlaylistGroupCollection
     */
    public function newCollection(array $models = []): PlaylistGroupCollection
    {
        return new PlaylistGroupCollection($models);
    }

    /**
     * Create a custom builder instance.
     *
     * @param $query
     *
     * @return PlaylistGroupBuilder
     */
    public function newEloquentBuilder($query): PlaylistGroupBuilder
    {
        return new PlaylistGroupBuilder($query);
    }

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, Enterprise::FOREIGN, self::PRIMARY);
    }

    public function playlistGroupMaps(): HasMany
    {
        return $this->hasMany(PlaylistGroupMap::class, self::FOREIGN, self::PRIMARY);
    }
}
