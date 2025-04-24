<?php

namespace App\Domains\Device\Model;

use App\Domains\Device\Model\Builder\DeviceCvedixGroupBuilder as Builder;
use App\Domains\Device\Model\Collection\DeviceCvedixGroupCollection;
use App\Domains\Device\Model\Collection\DeviceCvedixGroupCollection as Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * Class DeviceCvedixrtGroup
 *
 * @property int $id
 * @property string $group_name
 * @property string|null $description
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class DeviceCvedixrtGroup extends Model
{
    protected $table = 'device_cvedixrt_group';

    public const TABLE = 'device_cvedixrt_group';

    public const ID = 'id';

    public const FOREIGN_KEY = 'group_id';

    protected $fillable = [
        'group_name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];


    /**
     * Một Group có nhiều Solution, quan hệ 1-n
     *
     * @return HasMany
     */
    public function instances(): HasMany
    {
        return $this->hasMany(DeviceCvedixrtInstance::class, self::FOREIGN_KEY);
    }


    /**
     * @param array $models
     *
     * @return Collection
     */
    public function newCollection(array $models = []): Collection
    {
        return new DeviceCvedixGroupCollection($models);
    }

    /**
     * @param $query
     *
     * @return Builder
     */
    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }

}
