<?php declare(strict_types=1);

namespace App\Domains\Device\Model;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\DeviceGroup\Model\DeviceGroupModel;
use App\Domains\Display\Model\Display;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Device\Model\Builder\Device as Builder;
use App\Domains\Device\Model\Collection\Device as Collection;
use App\Domains\Device\Test\Factory\Device as TestFactory;
use App\Domains\DeviceMessage\Model\DeviceMessage as DeviceMessageModel;
use App\Domains\Position\Model\Position as PositionModel;
use App\Domains\Trip\Model\Trip as TripModel;
use App\Domains\User\Model\User as UserModel;
use App\Domains\Vehicle\Model\Vehicle as VehicleModel;
use App\Domains\User\Enterprise\Model\Enterprise;

/**
 * @property int $device_type_id
 * @property int|null $enterprise_id
 * @property int $camera_supported
 * @property int|mixed $camera_maximum
 * @property int|null $user_id
 * @property string|null $code
 * @property string $name
 * @property string $model
 * @property string $serial
 * @property string|null $phone_number
 * @property string $password
 * @property int $enabled
 * @property int $shared
 * @property int $shared_public
 * @property int|null $vehicle_id
 * @property int $enable_ai
 */
class Device extends ModelAbstract
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'device';

    /**
     * @const string
     */
    public const TABLE = 'device';

    /**
     * @const string
     */
    public const PRIMARY = 'id';

    /**
     * @const string
     */
    public const FOREIGN = 'device_id';

    /**
     * @var array<string, string>
     */

    /**
     * @var array
     */
    protected $guarded = [];

    protected $casts = [
        'enabled' => 'boolean',
        'shared' => 'boolean',
        'shared_public' => 'boolean',
    ];

    /**
     * @param array $models
     *
     * @return \App\Domains\Device\Model\Collection\Device
     */
    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return \App\Domains\Device\Model\Builder\Device
     */
    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }

    /**
     * @return \App\Domains\Device\Test\Factory\Device
     */
    protected static function newFactory(): TestFactory
    {
        return TestFactory::new();
    }

    /**
     * @return HasMany
     */
    public function positions(): HasMany
    {
        return $this->hasMany(PositionModel::class, static::FOREIGN);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function positionLast(): HasOne
    {
        return $this->hasOne(PositionModel::class, static::FOREIGN)
            ->ofMany(['date_utc_at' => 'MAX'], fn ($q) => $q->withoutGlobalScope('selectPointAsLatitudeLongitude'))
            ->selectOnlyLatitudeLongitude();
    }

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(DeviceMessageModel::class, static::FOREIGN);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tripLast(): HasOne
    {
        return $this->hasOne(TripModel::class, static::FOREIGN)
            ->ofMany(['end_utc_at' => 'MAX']);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tripLastShared(): HasOne
    {
        return $this->hasOne(TripModel::class, static::FOREIGN)
            ->ofMany(['endutc_at' => 'MAX'], fn ($q) => $q->whereShared());
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function tripLastSharedPublic(): HasOne
    {
        return $this->hasOne(TripModel::class, static::FOREIGN)
            ->ofMany(['end_utc_at' => 'MAX'], fn ($q) => $q->whereSharedPublic());
    }

    /**
     * @return HasMany
     */
    public function trips(): HasMany
    {
        return $this->hasMany(TripModel::class, static::FOREIGN);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, UserModel::FOREIGN);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, VehicleModel::FOREIGN);
    }

    public function deviceStatus()
    {
        return $this->hasOne(DeviceStatus::class, 'serial', 'serial');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deviceType(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class, 'device_type_id');
    }

    // quan hệ giữ device n-n schedule
    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(Schedule::class, 'display', Device::FOREIGN, Schedule::FOREIGN);
    }

    public function displays(): HasMany
    {
        return $this->hasMany(Display::class, self::FOREIGN);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }

    /**
     * @return HasMany
     */
    public function cameras(): HasMany
    {
        return $this->hasMany(Camera::class, 'device_id');
    }

    /**
     * @return BelongsToMany
     */
    public function deviceGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            DeviceGroupModel::class,
            'device_group_map',
            'device_id',
            'device_group_id'
        );
    }
}
