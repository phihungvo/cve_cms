<?php declare(strict_types=1);

namespace App\Domains\Solution\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Solution\Model\Builder\DeviceCvedixSolutionBuilder as Builder;
use App\Domains\Solution\Model\Collection\DeviceCvedixSolutionCollection as Collection;
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
class DeviceCvedixrtSolution extends ModelAbstract
{
    protected $table = 'device_cvedixrt_solution';

    public const TABLE = 'device_cvedixrt_solution';

    public const ID = 'id';

    public const FOREIGN_KEY = 'solution_id';

    protected $fillable = [
        'solution_name',
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
        return new Collection($models);
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
