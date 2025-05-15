<?php declare(strict_types=1);

namespace App\Domains\CvedixrtInstance\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\CvedixrtInstance\Model\Builder\CvedixrtInstanceBuilder;
use App\Domains\CvedixrtInstance\Model\Collection\CvedixrtInstanceCollection;


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
     * @return CvedixrtInstanceCollection
     */
    public function newCollection(array $models = []): CvedixrtInstanceCollection
    {
        return new CvedixrtInstanceCollection($models);
    }

    /**
     * Create a custom builder instance.
     *
     * @param $query
     *
     * @return CvedixrtInstanceBuilder
     */
    public function newEloquentBuilder($query): CvedixrtInstanceBuilder
    {
        return new CvedixrtInstanceBuilder($query);
    }

    //  TODO: Add your relationships and custom methods here
}
