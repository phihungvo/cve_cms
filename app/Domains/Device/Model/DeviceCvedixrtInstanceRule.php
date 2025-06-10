<?php declare(strict_types=1);

namespace App\Domains\Device\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Device\Model\Builder\DeviceCvedixInstanceRuleBuilder as Builder;
use App\Domains\Device\Model\Collection\DeviceCvedixInstanceRuleCollection as Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceCvedixrtInstanceRule extends ModelAbstract
{
    protected $table = 'device_cvedixrt_instance_rule';

    public const TABLE = 'device_cvedixrt_instance_rule';

    public const PRIMARY = 'id';

    public const FOREIGN_KEY = 'device_cvedixrt_instance_id';

    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'name',
        'detected_object',
        'rule_type',
        'drawing_object',
        'device_cvedixrt_instance_id',
    ];

    protected $casts = [
        'detected_object' => 'array', // json
        'rule_type' => 'string', // cast to string
        'direction' => 'string', // cast  to string
        'drawing_object' => 'array', // json
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
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
     * Khai báo quan hệ n-1 với bảng device_cvedixrt_instance
     *
     * @return BelongsTo
     */
    public function instance(): BelongsTo
    {
        return $this->belongsTo(DeviceCvedixrtInstance::class, DeviceCvedixrtInstance::FOREIGN_KEY);
    }
}
