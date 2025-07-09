<?php declare(strict_types=1);

namespace App\Domains\Device\Model;

use App\Domains\Campaign\Schedule\Model\Schedule;
use App\Domains\DeviceGroup\Model\DeviceGroupMap;
use App\Domains\DeviceGroup\Model\DeviceGroupModel;
use App\Domains\Display\Model\Display;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
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
 * @property int $instance_maximum
 * @property Collection $instances
 * @property Collection $allInstanceRules
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
    public const PRIMARY_KEY = 'id';

    /**
     * @const string
     */
    public const FOREIGN_KEY = 'device_id';

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
     * @return Collection
     */
    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return Builder
     */
    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }

    /**
     * @return TestFactory
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
        return $this->hasMany(PositionModel::class, static::FOREIGN_KEY);
    }

    /**
     * @return HasOne
     */
    public function positionLast(): HasOne
    {
        return $this->hasOne(PositionModel::class, static::FOREIGN_KEY)
            ->ofMany(['date_utc_at' => 'MAX'], fn ($q) => $q->withoutGlobalScope('selectPointAsLatitudeLongitude'))
            ->selectOnlyLatitudeLongitude();
    }

    /**
     * @return HasMany
     */
    public function messages(): HasMany
    {
        return $this->hasMany(DeviceMessageModel::class, static::FOREIGN_KEY);
    }

    /**
     * @return HasOne
     */
    public function tripLast(): HasOne
    {
        return $this->hasOne(TripModel::class, static::FOREIGN_KEY)
            ->ofMany(['end_utc_at' => 'MAX']);
    }

    /**
     * @return HasOne
     */
    public function tripLastShared(): HasOne
    {
        return $this->hasOne(TripModel::class, static::FOREIGN_KEY)
            ->ofMany(['endutc_at' => 'MAX'], fn ($q) => $q->whereShared());
    }

    /**
     * @return HasOne
     */
    public function tripLastSharedPublic(): HasOne
    {
        return $this->hasOne(TripModel::class, static::FOREIGN_KEY)
            ->ofMany(['end_utc_at' => 'MAX'], fn ($q) => $q->whereSharedPublic());
    }

    /**
     * @return HasMany
     */
    public function trips(): HasMany
    {
        return $this->hasMany(TripModel::class, static::FOREIGN_KEY);
    }

    /**
     * Quan hệ device n-1 với user
     *
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, UserModel::FOREIGN);
    }

    /**
     * Quan hệ device n-1 với vehicle
     *
     * @return BelongsTo
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(VehicleModel::class, VehicleModel::FOREIGN);
    }

    public function deviceStatus(): HasOne|Device
    {
        return $this->hasOne(DeviceStatus::class, 'serial', 'serial');
    }

    /**
     * Quan hệ device n-1 với device_type
     *
     * @return BelongsTo
     */
    public function deviceType(): BelongsTo
    {
        return $this->belongsTo(DeviceType::class, DeviceType::FOREIGN_KEY);
    }

    /**
     * Quan hệ device n-n với schedule
     *
     * @return BelongsToMany
     */
    public function schedules(): BelongsToMany
    {
        return $this->belongsToMany(
            Schedule::class,
            Display::TABLE,
            Device::FOREIGN_KEY,
            Schedule::FOREIGN
        );
    }

    /**
     * Quan hệ device 1-n với display
     *
     * @return HasMany
     */
    public function displays(): HasMany
    {
        return $this->hasMany(Display::class, self::FOREIGN_KEY);
    }

    /**
     * Quan hệ device n-1 với enterprise
     *
     * @return BelongsTo
     */
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, Enterprise::FOREIGN_KEY);
    }

    /**
     * Quan hệ device 1-n với camera
     *
     * @return HasMany
     */
    public function cameras(): HasMany
    {
        return $this->hasMany(Camera::class, self::FOREIGN_KEY);
    }

    /**
     * Quan hệ device n-n với device_group
     *
     * @return BelongsToMany
     */
    public function deviceGroups(): BelongsToMany
    {
        return $this->belongsToMany(
            DeviceGroupModel::class,
            DeviceGroupMap::TABLE,
            self::FOREIGN_KEY,
            DeviceGroupModel::FOREIGN_KEY
        );
    }

    /**
     * Quan hệ device 1-n với device_cvedixrt_instance
     *
     * @return HasMany
     */
    public function instances(): HasMany
    {
        return $this->hasMany(DeviceCvedixrtInstance::class, self::FOREIGN_KEY);
    }

    /**
     * Get all instance rules through instances (device 1-n instance 1-n instance_rule)
     *
     * @return HasManyThrough
     */
    public function allInstanceRules(): HasManyThrough
    {
        return $this->hasManyThrough(
            DeviceCvedixrtInstanceRule::class,
            DeviceCvedixrtInstance::class,
            self::FOREIGN_KEY, // Foreign key on DeviceCvedixrtInstance table...
            DeviceCvedixrtInstanceRule::FOREIGN_KEY, // Foreign key on DeviceCvedixrtInstanceRule table...
            self::PRIMARY_KEY, // Local key on Device table...
            DeviceCvedixrtInstance::PRIMARY_KEY // Local key on DeviceCvedixrtInstance table...
        );
    }

    public function events(): HasMany
    {
        return $this->hasMany(DeviceCvedixrtEvent::class, self::FOREIGN_KEY);
    }
}
