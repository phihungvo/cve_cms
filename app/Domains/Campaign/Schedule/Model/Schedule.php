<?php

declare(strict_types=1);

namespace App\Domains\Campaign\Schedule\Model;

use App\Domains\Campaign\Schedule\Model\Builder\ScheduleBuilder;
use App\Domains\Campaign\Schedule\Model\Collection\ScheduleCollection;
use App\Domains\CoreApp\Model\ModelAbstract;
// Import ModelAbstract
use App\Domains\Device\Model\Device;
use App\Domains\Device\Model\Device as DeviceModel;
use App\Domains\Display\Model\Display;
use App\Domains\Playlist\Model\PlaylistModel;
use App\Domains\ScheduleGroup\Model\ScheduleGroupMap;
use App\Domains\ScheduleGroup\Model\ScheduleGroupModel;
use App\Domains\User\Enterprise\Model\Enterprise;
use DateTime;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property DateTime $start_time
 * @property DateTime $end_time
 * @property int $enterprise_id
 */
class Schedule extends ModelAbstract // Kế thừa từ ModelAbstract thay vì Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'schedule';

    public const TABLE = 'schedule';

    public const PRIMARY = 'id';

    public const FOREIGN = 'schedule_id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
        'playlist_id',
        'start_time',
        'end_time',
        'repeat',
        'active',
        'enterprise_id',
    ];

    protected $casts = [
        'repeat' => 'boolean',
        'active' => 'boolean',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * Mối quan hệ: Một Schedule thuộc về một Playlist
     *
     * @return BelongsTo
     */
    public function playlist(): BelongsTo
    {
        return $this->belongsTo(PlaylistModel::class, PlaylistModel::FOREIGN, self::PRIMARY);
    }

    /**
     * Mối quan hệ: Một Schedule có nhiều Device thông qua bảng trung gian 'display'
     *
     * @return BelongsToMany
     */
    public function devices(): BelongsToMany
    {
        return $this->belongsToMany(DeviceModel::class, 'display', self::FOREIGN, Device::FOREIGN_KEY);
    }

    public function displays(): HasMany
    {
        return $this->hasMany(Display::class, 'schedule_id');
    }

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id', 'id');
    }

    /**
     * Scope: Lọc theo enterprise_id
     *
     * @param ScheduleBuilder $query
     *
     * @return ScheduleBuilder
     */
    public function scopeByEnterprise(ScheduleBuilder|\Illuminate\Database\Eloquent\Builder $query): ScheduleBuilder
    {
        return $query->where('enterprise_id', auth()->user()->enterprise_id);
    }

    /**
     * Khai báo quan hệ với ScheduleGroupModel thông qua bảng trung gian ScheduleGroupMap
     *
     * @return BelongsToMany
     */
    public function scheduleGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            ScheduleGroupModel::class,
            ScheduleGroupMap::TABLE,
            self::FOREIGN,
            ScheduleGroupModel::FOREIGN,
        );
    }

    /**
     * Tạo một instance của Collection tùy chỉnh, thay vì sử dụng Collection mặc định của Eloquent.
     * Không cần gọi trực tiếp phương thức này, Laravel sẽ tự động gọi khi cần thiết.
     *
     * @param array $models Mảng các mô hình để tạo Collection.
     *
     * @return ScheduleCollection Instance của PlaylistCollection tùy chỉnh.
     *
     * @see \Illuminate\Database\Eloquent\Model::newCollection()
     *
     * @overide
     */
    public function newCollection(array $models = []): ScheduleCollection
    {
        return new ScheduleCollection($models);
    }

    /**
     * Tạo một instance của Eloquent builder tùy chỉnh, thay vì sử dụng Builder mặc định của Eloquent.
     *
     *  Không cần gọi trực tiếp phương thức này, Laravel sẽ tự động gọi khi cần thiết.
     *
     * @param $query
     *
     * @return ScheduleBuilder
     *
     * @see \Illuminate\Database\Eloquent\Model::newEloquentBuilder()
     *
     * @overide
     */
    public function newEloquentBuilder($query): ScheduleBuilder
    {
        return new ScheduleBuilder($query);
    }
}
