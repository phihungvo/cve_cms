<?php
declare(strict_types=1);

namespace App\Domains\Playlist\Model;

use App\Domains\Campaign\Media\Model\Media;
use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Device\Model\Device;
use App\Domains\Display\Model\Display;
use App\Domains\Playlist\Model\Builder\PlaylistBuilder;
use App\Domains\Playlist\Model\Collection\PlaylistCollection;
use App\Domains\Playlist\PlaylistGroup\Model\PlaylistGroupModel;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property int|null $enterprise_id
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domains\Display\Model> $displays
 * @property-read int|null $displays_count
 * @property-read Enterprise|null $enterprise
 * @property-read \App\Domains\Campaign\Media\Model\Collection\MediaCollection<int, \App\Domains\Campaign\Media\Model\Media> $medias
 * @property-read int|null $medias_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Domains\Playlist\Model\PlaylistMediaModel> $playlistMedia
 * @property-read int|null $playlist_media_count
 * @property-read \App\Domains\Campaign\Schedule\Model\Collection\ScheduleCollection<int, Schedule> $schedules
 * @property-read int|null $schedules_count
 *
 * @method static PlaylistBuilder<static>|PlaylistModel addTable(array|string $column)
 * @method static PlaylistBuilder<static>|PlaylistModel addTableRaw(array|string $column)
 * @method static PlaylistCollection<int, static> all($columns = ['*'])
 * @method static PlaylistBuilder<static>|PlaylistModel byCreatedAtAfter(string $created_at)
 * @method static PlaylistBuilder<static>|PlaylistModel byDeviceId(int $device_id)
 * @method static PlaylistBuilder<static>|PlaylistModel byDeviceIds(array $device_ids)
 * @method static PlaylistBuilder<static>|PlaylistModel byEnterprise()
 * @method static PlaylistBuilder<static>|PlaylistModel byId(int $id)
 * @method static PlaylistBuilder<static>|PlaylistModel byIdNext(int $id)
 * @method static PlaylistBuilder<static>|PlaylistModel byIdNot(int $id)
 * @method static PlaylistBuilder<static>|PlaylistModel byIdPrevious(int $id)
 * @method static PlaylistBuilder<static>|PlaylistModel byIds(array $ids)
 * @method static PlaylistBuilder<static>|PlaylistModel byIdsNot(array $ids)
 * @method static PlaylistBuilder<static>|PlaylistModel byUpdatedAtAfter(string $updated_at)
 * @method static PlaylistBuilder<static>|PlaylistModel byUserId(int $user_id)
 * @method static PlaylistBuilder<static>|PlaylistModel byUserOrManager(\App\Domains\User\Model\User $user)
 * @method static PlaylistBuilder<static>|PlaylistModel byVehicleId(int $vehicle_id)
 * @method static PlaylistBuilder<static>|PlaylistModel byVehicleIds(array $vehicle_ids)
 * @method static PlaylistBuilder<static>|PlaylistModel db()
 * @method static PlaylistBuilder<static>|PlaylistModel enabled(bool $enabled = true)
 * @method static PlaylistCollection<int, static> get($columns = ['*'])
 * @method static PlaylistBuilder<static>|PlaylistModel getTable()
 * @method static PlaylistBuilder<static>|PlaylistModel kiemTraRole(int $id)
 * @method static PlaylistBuilder<static>|PlaylistModel list()
 * @method static PlaylistBuilder<static>|PlaylistModel newModelQuery()
 * @method static PlaylistBuilder<static>|PlaylistModel newQuery()
 * @method static Builder<static>|PlaylistModel onlyTrashed()
 * @method static PlaylistBuilder<static>|PlaylistModel orWhereStringInRaw(string $column, array $strings)
 * @method static PlaylistBuilder<static>|PlaylistModel orderByColumn(string $column, ?string $mode)
 * @method static PlaylistBuilder<static>|PlaylistModel orderByCreatedAtAsc()
 * @method static PlaylistBuilder<static>|PlaylistModel orderByCreatedAtDesc()
 * @method static PlaylistBuilder<static>|PlaylistModel orderByFirst()
 * @method static PlaylistBuilder<static>|PlaylistModel orderByLast()
 * @method static PlaylistBuilder<static>|PlaylistModel orderByUpdatedAtAsc()
 * @method static PlaylistBuilder<static>|PlaylistModel orderByUpdatedAtDesc()
 * @method static PlaylistBuilder<static>|PlaylistModel orderMode(?string $mode, string $default = 'ASC')
 * @method static PlaylistBuilder<static>|PlaylistModel query()
 * @method static PlaylistBuilder<static>|PlaylistModel roleOwner()
 * @method static PlaylistBuilder<static>|PlaylistModel roleRoot()
 * @method static PlaylistBuilder<static>|PlaylistModel selectOnly(string ...$columns)
 * @method static PlaylistBuilder<static>|PlaylistModel whenDeviceId(?int $device_id)
 * @method static PlaylistBuilder<static>|PlaylistModel whenEnterprise(?int $enterprise_id)
 * @method static PlaylistBuilder<static>|PlaylistModel whenId(?int $id)
 * @method static PlaylistBuilder<static>|PlaylistModel whenIdNext(int $id)
 * @method static PlaylistBuilder<static>|PlaylistModel whenIds(?array $ids)
 * @method static PlaylistBuilder<static>|PlaylistModel whenUserId(?int $user_id)
 * @method static PlaylistBuilder<static>|PlaylistModel whenVehicleId(?int $vehicle_id)
 * @method static PlaylistBuilder<static>|PlaylistModel whereCreatedAt($value)
 * @method static PlaylistBuilder<static>|PlaylistModel whereDeletedAt($value)
 * @method static PlaylistBuilder<static>|PlaylistModel whereDescription($value)
 * @method static PlaylistBuilder<static>|PlaylistModel whereEnterpriseId($value)
 * @method static PlaylistBuilder<static>|PlaylistModel whereId($value)
 * @method static PlaylistBuilder<static>|PlaylistModel whereName($value)
 * @method static PlaylistBuilder<static>|PlaylistModel whereStringInRaw(string $column, array $strings)
 * @method static PlaylistBuilder<static>|PlaylistModel whereUpdatedAt($value)
 * @method static PlaylistBuilder<static>|PlaylistModel withEnterprise()
 * @method static PlaylistBuilder<static>|PlaylistModel withSimple(string $relation)
 * @method static Builder<static>|PlaylistModel withTrashed()
 * @method static PlaylistBuilder<static>|PlaylistModel withUser()
 * @method static Builder<static>|PlaylistModel withoutTrashed()
 * @method static PlaylistBuilder<static>|PlaylistModel wrap(string $column)
 *
 * @mixin \Eloquent
 */
class PlaylistModel extends ModelAbstract
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
    protected $table = 'playlist';

    /**
     * @const string
     */
    public const TABLE = 'playlist';

    /**
     * @const string
     */
    public const FOREIGN = 'playlist_id';

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

    protected $dates = ['deleted_at']; // Khai báo cột deleted_at là kiểu ngày tháng

    /**
     * Một playlists có nhiều Media thông qua `playlist_media`
     *
     * @return BelongsToMany
     */
    public function medias(): BelongsToMany
    {
        return $this->belongsToMany(
            Media::class,
            PlaylistMediaModel::TABLE,
            self::FOREIGN,
            Media::FOREIGN
        )
            ->withPivot('position')
            ->withTimestamps()
            ->orderBy('position', 'asc');
    }

    /**
     * @return HasMany
     */
    public function playlistMedia(): hasMany
    {
        return $this->hasMany(PlaylistMediaModel::class, self::FOREIGN, self::PRIMARY);
    }

    /**
     * Một playlists có nhiều schedule thông qua `schedule_detail`
     *
     * @return HasOne
     */
    public function schedules(): HasOne
    {
        return $this->hasOne(
            Schedule::class,
            self::FOREIGN,
            self::PRIMARY
        );
    }

    /**
     * Một playlists thuộc về một enterprise
     *
     * @return BelongsTo
     */
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, Enterprise::FOREIGN, self::PRIMARY);
    }

    /**
     * Một playlist có nhiều display, trong bảng display có chứa playlist_id
     *
     * @return HasMany
     */
    public function displays(): HasMany
    {
        return $this->hasMany(Display::class, self::FOREIGN, self::PRIMARY);
    }

    /**
     * Một playlist có nhiều device thông qua display
     *
     * @return Builder|HasManyThrough
     */
    public function devices(): belongsToMany
    {
        return $this->belongsToMany(Device::class, Display::TABLE, self::FOREIGN, Device::FOREIGN);
    }

    public function playlistGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            PlaylistGroupModel::class,
            'playlist_group_map',
            self::FOREIGN,
            PlaylistGroupModel::FOREIGN
        );
    }

    /**
     * Tạo một instance của Collection tùy chỉnh, thay vì sử dụng Collection mặc định của Eloquent.
     * Không cần gọi trực tiếp phương thức này, Laravel sẽ tự động gọi khi cần thiết.
     *
     * @param array $models Mảng các mô hình để tạo Collection.
     *
     * @return PlaylistCollection Instance của PlaylistCollection tùy chỉnh.
     *
     * @see \Illuminate\Database\Eloquent\Model::newCollection()
     *
     * @override
     */
    public function newCollection(array $models = []): PlaylistCollection
    {
        return new PlaylistCollection($models);
    }

    /**
     * Tạo một instance của Eloquent builder tùy chỉnh, thay vì sử dụng Builder mặc định của Eloquent.
     * Không cần gọi trực tiếp phương thức này, Laravel sẽ tự động gọi khi cần thiết.
     *
     * @param $query
     *
     * @return PlaylistBuilder
     *
     * @see \Illuminate\Database\Eloquent\Model::newEloquentBuilder()
     *
     * @override
     */
    public function newEloquentBuilder($query): PlaylistBuilder
    {
        return new PlaylistBuilder($query);
    }
}
