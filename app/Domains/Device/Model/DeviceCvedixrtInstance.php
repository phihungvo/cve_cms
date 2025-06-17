<?php declare(strict_types=1);

namespace App\Domains\Device\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Device\Model\Builder\DeviceCvedixInstanceBuilder as Builder;
use App\Domains\Device\Model\Collection\DeviceCvedixInstanceCollection as Collection;
use App\Domains\Group\Model\DeviceCvedixrtGroup;
use App\Domains\Solution\Model\DeviceCvedixrtSolution;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeviceCvedixrtInstance extends ModelAbstract
{
    protected $table = 'device_cvedixrt_instance';

    public const TABLE = 'device_cvedixrt_instance';

    public const PRIMARY_KEY = 'id';

    public const FOREIGN_KEY = 'device_cvedixrt_instance_id';

    protected $fillable = [
        'uuid',
        'instance_name',
        'input_source',
        'device_id',
        'solution_id',
        'group_id',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * @return array
     */
    protected function casts(): array
    {
        return [
        ];
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    /**
     * Một Instance thuộc về 1 Solution, quan hệ n-1
     *
     * @return BelongsTo
     */
    public function solution(): BelongsTo
    {
        return $this->belongsTo(DeviceCvedixrtSolution::class, DeviceCvedixrtSolution::FOREIGN_KEY);
    }

    /**
     * Một Instance thuộc về 1 Group, quan hệ n-1
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(DeviceCvedixrtGroup::class, DeviceCvedixrtGroup::FOREIGN_KEY);
    }

    public function instanceRules(): HasMany
    {
        return $this->hasMany(DeviceCvedixrtInstanceRule::class, self::FOREIGN_KEY);
    }

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
}
