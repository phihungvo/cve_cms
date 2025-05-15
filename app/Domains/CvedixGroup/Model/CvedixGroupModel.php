<?php declare(strict_types=1);

namespace App\Domains\CvedixGroup\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\CvedixGroup\Model\Builder\CvedixGroupBuilder;
use App\Domains\CvedixGroup\Model\Collection\CvedixGroupCollection;


class CvedixGroupModel extends ModelAbstract
{
    use HasFactory;
    

    /**
     * @const string
     */
    const PRIMARY = 'id';

    /**
     * @var string
     */
    protected $table = 'cvedix_group';

    /**
     * @const string
     */
    public const TABLE = 'cvedix_group';

    /**
     * @const string
     */
    public const FOREIGN = 'cvedix_group_id';

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
     * @return CvedixGroupCollection
     */
    public function newCollection(array $models = []): CvedixGroupCollection
    {
        return new CvedixGroupCollection($models);
    }

    /**
     * Create a custom builder instance.
     *
     * @param $query
     *
     * @return CvedixGroupBuilder
     */
    public function newEloquentBuilder($query): CvedixGroupBuilder
    {
        return new CvedixGroupBuilder($query);
    }

    //  TODO: Add your relationships and custom methods here
}
