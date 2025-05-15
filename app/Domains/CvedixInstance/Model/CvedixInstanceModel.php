<?php declare(strict_types=1);

namespace App\Domains\CvedixInstance\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\CvedixInstance\Model\Builder\CvedixInstanceBuilder;
use App\Domains\CvedixInstance\Model\Collection\CvedixInstanceCollection;


class CvedixInstanceModel extends ModelAbstract
{
    use HasFactory;
    

    /**
     * @const string
     */
    const PRIMARY = 'id';

    /**
     * @var string
     */
    protected $table = 'cvedix_instance';

    /**
     * @const string
     */
    public const TABLE = 'cvedix_instance';

    /**
     * @const string
     */
    public const FOREIGN = 'cvedix_instance_id';

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
     * @return CvedixInstanceCollection
     */
    public function newCollection(array $models = []): CvedixInstanceCollection
    {
        return new CvedixInstanceCollection($models);
    }

    /**
     * Create a custom builder instance.
     *
     * @param $query
     *
     * @return CvedixInstanceBuilder
     */
    public function newEloquentBuilder($query): CvedixInstanceBuilder
    {
        return new CvedixInstanceBuilder($query);
    }

    //  TODO: Add your relationships and custom methods here
}
