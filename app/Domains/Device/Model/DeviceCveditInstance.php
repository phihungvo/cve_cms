<?php

namespace App\Domains\Device\Model;

use App\Domains\Device\Model\Builder\DeviceCvedixInstanceBuilder as Builder;
use App\Domains\Device\Model\Collection\DeviceCvedixInstanceCollection as Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceCveditInstance extends Model
{
    protected $table = 'device_cvedit_instance';

    public const TABLE = 'device_cvedit_instance';

    public const ID = 'id';

    protected $fillable = [
        'uuid',
        'instance_name',
        'instance_source',
        'device_id',
        'solution_id',
        'group_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function solution(): BelongsTo
    {
        return $this->belongsTo(DeviceCvedixSolution::class, 'solution_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(DeviceCveditGroup::class, 'group_id');
    }

    protected function casts(): array
    {
        return [
            'zones' => 'array',
            'lines' => 'array',
        ];
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
