<?php declare(strict_types=1);

namespace App\Domains\CvedixrtGroup\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\CvedixrtGroup\Model\Builder\CvedixrtGroupBuilder;
use App\Domains\CvedixrtGroup\Model\Collection\CvedixrtGroupCollection;


class CvedixrtGroupModel extends ModelAbstract
{
    use HasFactory;
    

    /**
     * @const string
     */
    const PRIMARY = 'id';

    /**
     * @var string
     */
    protected $table = 'cvedixrt_group';

    /**
     * @const string
     */
    public const TABLE = 'cvedixrt_group';

    /**
     * @const string
     */
    public const FOREIGN = 'cvedixrt_group_id';

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
     * @return CvedixrtGroupCollection
     */
    public function newCollection(array $models = []): CvedixrtGroupCollection
    {
        return new CvedixrtGroupCollection($models);
    }

    /**
     * Create a custom builder instance.
     *
     * @param $query
     *
     * @return CvedixrtGroupBuilder
     */
    public function newEloquentBuilder($query): CvedixrtGroupBuilder
    {
        return new CvedixrtGroupBuilder($query);
    }

    //  TODO: Add your relationships and custom methods here
}
