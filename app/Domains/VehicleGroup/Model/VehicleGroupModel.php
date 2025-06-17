<?php declare(strict_types=1);

namespace App\Domains\VehicleGroup\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\VehicleGroup\Model\Builder\VehicleGroupBuilder;
use App\Domains\VehicleGroup\Model\Collection\VehicleGroupCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class VehicleGroupModel extends ModelAbstract
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
    protected $table = 'vehicle_group';

    /**
     * @const string
     */
    public const TABLE = 'vehicle_group';

    /**
     * @const string
     */
    public const FOREIGN = 'vehicle_group_id';

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
     * @return VehicleGroupCollection
     */
    public function newCollection(array $models = []): VehicleGroupCollection
    {
        return new VehicleGroupCollection($models);
    }

    /**
     * Create a custom builder instance.
     *
     * @param $query
     *
     * @return VehicleGroupBuilder
     */
    public function newEloquentBuilder($query): VehicleGroupBuilder
    {
        return new VehicleGroupBuilder($query);
    }

    public function vehicleGroupsMaps()
    {
        return $this->hasMany(VehicleGroupMap::class, self::FOREIGN, self::PRIMARY);
    }

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, Enterprise::FOREIGN_KEY, self::PRIMARY);
    }
}
