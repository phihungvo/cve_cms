<?php declare(strict_types=1);

namespace App\Domains\DeviceGroup\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\DeviceGroup\Model\Builder\DeviceGroupBuilder;
use App\Domains\DeviceGroup\Model\Collection\DeviceGroupCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property string $name
 * @property string|null $description
 * @property int|null $enterprise_id
 */
class DeviceGroupModel extends ModelAbstract
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @const string
     */
    const PRIMARY_KEY = 'id';

    /**
     * @var string
     */
    protected $table = 'device_group';

    /**
     * @const string
     */
    public const TABLE = 'device_group';

    /**
     * @const string
     */
    public const FOREIGN_KEY = 'device_group_id';

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
     * Define the relationship with the Enterprise model.
     *
     * 1 device group belongs to 1 enterprise.
     *
     * @return BelongsTo
     */
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, Enterprise::FOREIGN_KEY, Enterprise::PRIMARY);
    }

    public function deviceGroupMaps(): HasMany
    {
        return $this->hasMany(DeviceGroupMap::class, DeviceGroupModel::FOREIGN_KEY, DeviceGroupModel::PRIMARY_KEY);
    }

    /**
     * Create a custom collection instance.
     *
     * @param array $models
     *
     * @return DeviceGroupCollection
     */
    public function newCollection(array $models = []): DeviceGroupCollection
    {
        return new DeviceGroupCollection($models);
    }

    /**
     * Create a custom builder instance.
     *
     * @param $query
     *
     * @return DeviceGroupBuilder
     */
    public function newEloquentBuilder($query): DeviceGroupBuilder
    {
        return new DeviceGroupBuilder($query);
    }
}
