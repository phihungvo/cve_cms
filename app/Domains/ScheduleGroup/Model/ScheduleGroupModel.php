<?php declare(strict_types=1);

namespace App\Domains\ScheduleGroup\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Enterprise\Model\Enterprise;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\ScheduleGroup\Model\Builder\ScheduleGroupBuilder;
use App\Domains\ScheduleGroup\Model\Collection\ScheduleGroupCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class ScheduleGroupModel extends ModelAbstract
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
    protected $table = 'schedule_group';

    /**
     * @const string
     */
    public const TABLE = 'schedule_group';

    /**
     * @const string
     */
    public const FOREIGN = 'schedule_group_id';

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
     * @return ScheduleGroupCollection
     */
    public function newCollection(array $models = []): ScheduleGroupCollection
    {
        return new ScheduleGroupCollection($models);
    }

    /**
     * Create a custom builder instance.
     *
     * @param $query
     *
     * @return ScheduleGroupBuilder
     */
    public function newEloquentBuilder($query): ScheduleGroupBuilder
    {
        return new ScheduleGroupBuilder($query);
    }

    public function scheduleGroups(): HasMany
    {
        return $this->hasMany(ScheduleGroupMap::class, ScheduleGroupMap::FOREIGN, self::PRIMARY);
    }

    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, Enterprise::FOREIGN, self::PRIMARY);
    }
}
