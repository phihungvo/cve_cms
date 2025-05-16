<?php declare(strict_types=1);

namespace App\Domains\CvedixrtSolution\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\CvedixrtInstance\Model\CvedixrtInstanceModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\CvedixrtSolution\Model\Builder\CvedixrtSolutionBuilder;
use App\Domains\CvedixrtSolution\Model\Collection\CvedixrtSolutionCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;


class CvedixrtSolutionModel extends ModelAbstract
{
    use HasFactory;


    /**
     * @const string
     */
    const PRIMARY = 'id';

    /**
     * @var string
     */
    protected $table = 'cvedixrt_solution';

    /**
     * @const string
     */
    public const TABLE = 'cvedixrt_solution';

    /**
     * @const string
     */
    public const FOREIGN = 'solution_id';

    public $timestamps = true;

    protected $fillable = [
        'name',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Create a custom collection instance.
     *
     * @param array $models
     *
     * @return CvedixrtSolutionCollection
     */
    public function newCollection(array $models = []): CvedixrtSolutionCollection
    {
        return new CvedixrtSolutionCollection($models);
    }

    /**
     * Create a custom builder instance.
     *
     * @param $query
     *
     * @return CvedixrtSolutionBuilder
     */
    public function newEloquentBuilder($query): CvedixrtSolutionBuilder
    {
        return new CvedixrtSolutionBuilder($query);
    }

    /**
     * Khai báo quan hệ 1-n với bảng cvedixrt_instance
     *
     * @return HasMany
     */
    public function instances(): HasMany
    {
        return $this->hasMany(CvedixrtInstanceModel::class, self::FOREIGN, CvedixrtInstanceModel::PRIMARY);
    }
}
