<?php declare(strict_types=1);

namespace App\Domains\CvedixSolution\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\CvedixSolution\Model\Builder\CvedixSolutionBuilder;
use App\Domains\CvedixSolution\Model\Collection\CvedixSolutionCollection;


class CvedixSolutionModel extends ModelAbstract
{
    use HasFactory;
    

    /**
     * @const string
     */
    const PRIMARY = 'id';

    /**
     * @var string
     */
    protected $table = 'cvedix_solution';

    /**
     * @const string
     */
    public const TABLE = 'cvedix_solution';

    /**
     * @const string
     */
    public const FOREIGN = 'cvedix_solution_id';

    public $timestamps = true;

    protected $fillable = [
        // TODO: Add your fillable fields here
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
     * @return CvedixSolutionCollection
     */
    public function newCollection(array $models = []): CvedixSolutionCollection
    {
        return new CvedixSolutionCollection($models);
    }

    /**
     * Create a custom builder instance.
     *
     * @param $query
     *
     * @return CvedixSolutionBuilder
     */
    public function newEloquentBuilder($query): CvedixSolutionBuilder
    {
        return new CvedixSolutionBuilder($query);
    }

    //  TODO: Add your relationships and custom methods here
}
