<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Cvedixrt\Group\Model\CvedixrtGroupModel;
use App\Domains\Cvedixrt\Solution\Model\CvedixrtSolutionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Cvedixrt\Instance\Model\Builder\CvedixrtInstanceBuilder as Builder;
use App\Domains\Cvedixrt\Instance\Model\Collection\CvedixrtInstanceCollection as Collection;

class CvedixrtInstanceModel extends ModelAbstract
{
    use HasFactory;

    /**
     * @const string
     */
    const PRIMARY = 'id';

    /**
     * @var string
     */
    protected $table = 'cvedixrt_instance';

    /**
     * @const string
     */
    public const TABLE = 'cvedixrt_instance';

    /**
     * @const string
     */
    public const FOREIGN = 'cvedixrt_instance_id';

    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'name',
        'source',
        'solution_id',
        'group_id',
        'description',
        'zones',
        'lines',
    ];

    protected $casts = [
        'lines' => 'array',
        'zones' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Create a custom collection instance.
     *
     * @param array $models
     *
     * @return Collection
     */
    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
    }

    /**
     * Create a custom builder instance.
     *
     * @param $query
     *
     * @return Builder
     */
    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }

    /**
     * Khai báo quan hệ n-1 với bảng cvedixrt_solution
     *
     * @return BelongsTo
     */
    public function solution(): BelongsTo
    {
        return $this->belongsTo(CvedixrtSolutionModel::class, CvedixrtSolutionModel::FOREIGN);
    }

    /**
     * Khai báo quan hệ n-1 với bảng cvedixrt_group
     *
     * @return BelongsTo
     */
    public function group(): BelongsTo
    {
        return $this->belongsTo(CvedixrtGroupModel::class, CvedixrtGroupModel::FOREIGN);
    }

    public function instanceRules()
    {
        return $this->hasMany(CvedixrtInstanceRuleModel::class, 'cvedixrt_instance_id');
    }
}
