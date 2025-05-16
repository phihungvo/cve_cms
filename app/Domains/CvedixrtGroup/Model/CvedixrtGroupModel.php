<?php declare(strict_types=1);

namespace App\Domains\CvedixrtGroup\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\CvedixrtInstance\Model\CvedixrtInstanceModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\CvedixrtGroup\Model\Builder\CvedixrtGroupBuilder;
use App\Domains\CvedixrtGroup\Model\Collection\CvedixrtGroupCollection;
use Illuminate\Database\Eloquent\Relations\HasMany;


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
    public const FOREIGN = 'group_id';

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
