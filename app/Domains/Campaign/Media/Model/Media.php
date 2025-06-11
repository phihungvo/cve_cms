<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Media\Model;

use App\Domains\Campaign\Media\Model\Builder\MediaBuilder;
use App\Domains\Campaign\Media\Model\Collection\MediaCollection;
use App\Domains\Campaign\Model\Campaign;
use App\Domains\Playlist\Model\PlaylistMediaModel;
use App\Domains\Playlist\Model\PlaylistModel;
use App\Domains\Core\Traits\Factory;
use App\Domains\CoreApp\Model\ModelAbstract;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\User\Enterprise\Model\Enterprise;

/**
 * @property int $id
 * @property string $name
 * @property string $file_name
 * @property string $media_url
 * @property int|null $size
 * @property string|null $type
 * @property int|null $duration
 * @property int|null $campaign_id
 * @property int|null $enterprise_id
 * @property \Illuminate\Support\Carbon $created_at
 * @property \Illuminate\Support\Carbon $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read Campaign|null $campaign
 * @property-read Enterprise|null $enterprise
 * @property-read \App\Domains\Playlist\Model\Collection\PlaylistCollection<int, PlaylistModel> $playlists
 * @property-read int|null $playlists_count
 *
 * @method static MediaBuilder<static>|Media addTable(array|string $column)
 * @method static MediaBuilder<static>|Media addTableRaw(array|string $column)
 * @method static MediaCollection<int, static> all($columns = ['*'])
 * @method static MediaBuilder<static>|Media byCreatedAtAfter(string $created_at)
 * @method static MediaBuilder<static>|Media byDeviceId(int $device_id)
 * @method static MediaBuilder<static>|Media byDeviceIds(array $device_ids)
 * @method static MediaBuilder<static>|Media byEnterprise()
 * @method static MediaBuilder<static>|Media byId(int $id)
 * @method static MediaBuilder<static>|Media byIdNext(int $id)
 * @method static MediaBuilder<static>|Media byIdNot(int $id)
 * @method static MediaBuilder<static>|Media byIdPrevious(int $id)
 * @method static MediaBuilder<static>|Media byIds(array $ids)
 * @method static MediaBuilder<static>|Media byIdsNot(array $ids)
 * @method static MediaBuilder<static>|Media byUpdatedAtAfter(string $updated_at)
 * @method static MediaBuilder<static>|Media byUserId(int $user_id)
 * @method static MediaBuilder<static>|Media byUserOrManager(\App\Domains\User\Model\User $user)
 * @method static MediaBuilder<static>|Media byVehicleId(int $vehicle_id)
 * @method static MediaBuilder<static>|Media byVehicleIds(array $vehicle_ids)
 * @method static MediaBuilder<static>|Media db()
 * @method static MediaBuilder<static>|Media enabled(bool $enabled = true)
 * @method static MediaCollection<int, static> get($columns = ['*'])
 * @method static MediaBuilder<static>|Media getTable()
 * @method static MediaBuilder<static>|Media list()
 * @method static MediaBuilder<static>|Media newModelQuery()
 * @method static MediaBuilder<static>|Media newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media onlyTrashed()
 * @method static MediaBuilder<static>|Media orWhereStringInRaw(string $column, array $strings)
 * @method static MediaBuilder<static>|Media orderByColumn(string $column, ?string $mode)
 * @method static MediaBuilder<static>|Media orderByCreatedAtAsc()
 * @method static MediaBuilder<static>|Media orderByCreatedAtDesc()
 * @method static MediaBuilder<static>|Media orderByFirst()
 * @method static MediaBuilder<static>|Media orderByLast()
 * @method static MediaBuilder<static>|Media orderByUpdatedAtAsc()
 * @method static MediaBuilder<static>|Media orderByUpdatedAtDesc()
 * @method static MediaBuilder<static>|Media orderMode(?string $mode, string $default = 'ASC')
 * @method static MediaBuilder<static>|Media query()
 * @method static MediaBuilder<static>|Media roleOwner()
 * @method static MediaBuilder<static>|Media roleRoot()
 * @method static MediaBuilder<static>|Media selectOnly(string ...$columns)
 * @method static MediaBuilder<static>|Media whenDeviceId(?int $device_id)
 * @method static MediaBuilder<static>|Media whenEnterprise(?int $enterprise_id)
 * @method static MediaBuilder<static>|Media whenId(?int $id)
 * @method static MediaBuilder<static>|Media whenIdNext(int $id)
 * @method static MediaBuilder<static>|Media whenIds(?array $ids)
 * @method static MediaBuilder<static>|Media whenUserId(?int $user_id)
 * @method static MediaBuilder<static>|Media whenVehicleId(?int $vehicle_id)
 * @method static MediaBuilder<static>|Media whereCampaignId($value)
 * @method static MediaBuilder<static>|Media whereCreatedAt($value)
 * @method static MediaBuilder<static>|Media whereDeletedAt($value)
 * @method static MediaBuilder<static>|Media whereDuration($value)
 * @method static MediaBuilder<static>|Media whereEnterpriseId($value)
 * @method static MediaBuilder<static>|Media whereFileName($value)
 * @method static MediaBuilder<static>|Media whereId($value)
 * @method static MediaBuilder<static>|Media whereMediaUrl($value)
 * @method static MediaBuilder<static>|Media whereName($value)
 * @method static MediaBuilder<static>|Media whereSize($value)
 * @method static MediaBuilder<static>|Media whereStringInRaw(string $column, array $strings)
 * @method static MediaBuilder<static>|Media whereType($value)
 * @method static MediaBuilder<static>|Media whereUpdatedAt($value)
 * @method static MediaBuilder<static>|Media withSimple(string $relation)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media withTrashed()
 * @method static MediaBuilder<static>|Media withUser()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Media withoutTrashed()
 * @method static MediaBuilder<static>|Media wrap(string $column)
 *
 * @mixin \Eloquent
 */
class Media extends ModelAbstract
{
    use Factory;
    use SoftDeletes;

    /**
     * @const string
     */
    const PRIMARY = 'id';

    protected $table = 'media';

    /**
     * @const string
     */
    public const TABLE = 'media';

    /**
     * @const string
     */
    const FOREIGN = 'media_id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'file_name',
        'media_url',
        'size',
        'type',
        'duration',
        'campaign_id',
        'enterprise_id',
    ];

    protected $dates = ['deleted_at'];

    protected $casts = [
        'size' => 'integer',
        'duration' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Một Media thuộc về nhiều playlists thông qua bảng trung gian `playlist_media`
     *
     * @return BelongsToMany
     */
    public function playlists(): BelongsToMany
    {
        return $this->belongsToMany(
            PlaylistModel::class,
            PlaylistMediaModel::TABLE,
            self::FOREIGN,
            PlaylistModel::FOREIGN
        )
            ->withPivot('position')
            ->withTimestamps();
    }

    /**
     * Một Media thuộc về một campaign
     *
     * @return BelongsTo
     */
    public function campaign(): BelongsTo
    {
        return $this->belongsTo(
            Campaign::class,
            Campaign::FOREIGN,
            self::PRIMARY
        );
    }

    /**
     * Một Media thuộc về một enterprise
     *
     * @return BelongsTo
     */
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(
            Enterprise::class,
            Enterprise::FOREIGN,
            self::PRIMARY
        );
    }

    /**
     * Tạo một instance của collection tùy chỉnh, thay vì sử dụng Collection mặc định
     * Không cần gọi phương thức này, Laravel sẽ tự động gọi khi cần thiết
     *
     * @param array $models
     *
     * @return MediaCollection
     *
     * @see \Illuminate\Database\Eloquent\Model::newCollection()
     *
     * @overide
     */
    public function newCollection(array $models = []): MediaCollection
    {
        return new MediaCollection($models);
    }

    /**
     * Tạo một instance của Eloquen buider tùy chỉnh, thay vì sử dụng Eloquent mặc định
     * Không cần phải override phương thức này, Laravel sẽ tự động gọi khi cần thiết
     *
     * @param $query
     *
     * @return MediaBuilder
     *
     * @see \Illuminate\Database\Eloquent\Model::newEloquentBuilder()
     *
     * @override
     */
    public function newEloquentBuilder($query): MediaBuilder
    {
        return new MediaBuilder($query);
    }
}
