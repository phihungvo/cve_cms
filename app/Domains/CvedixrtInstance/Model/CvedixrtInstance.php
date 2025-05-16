<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\CvedixrtGroup\Model\CvedixrtGroupModel;
use App\Domains\CvedixrtSolution\Model\CvedixrtSolutionModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\CvedixrtInstance\Model\Builder\CvedixrtInstanceBuilder as Builder;
use App\Domains\CvedixrtInstance\Model\Collection\CvedixrtInstanceCollection as Collection;

class CvedixrtInstance extends ModelAbstract
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
        'instance_name',
        'input_source',
        'solution_id',
        'group_id',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected function casts(): array
    {
        return [
            'zones' => 'array',
            'lines' => 'array',
        ];
    }

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

    public function solution(): BelongsTo
    {
        return $this->belongsTo(CvedixrtSolutionModel::class, 'solution_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(CvedixrtGroupModel::class, 'group_id');
    }

}
