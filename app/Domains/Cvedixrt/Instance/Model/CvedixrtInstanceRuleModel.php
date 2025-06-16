<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Instance\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\Cvedixrt\Instance\Model\Builder\CvedixrtInstanceRuleBuilder as Builder;
use App\Domains\Cvedixrt\Instance\Model\Collection\CvedixrtInstanceRuleCollection as Collection;

class CvedixrtInstanceRuleModel extends ModelAbstract
{
    use HasFactory;

    /**
     * @const string
     */
    const PRIMARY_KEY = 'id';

    /**
     * @var string
     */
    protected $table = 'cvedixrt_instance_rule';

    /**
     * @const string
     */
    public const TABLE = 'cvedixrt_instance_rule';

    /**
     * @const string
     */
    public const FOREIGN_KEY = 'instance_rule_id';

    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'name',
        'detected_object',
        'direction',
        'rule_type',
        'drawing_object',
        'cvedixrt_instance_id',
    ];

    protected $casts = [
        'detected_object' => 'array', // json
        'rule_type' => 'string', // cast to string
        'direction' => 'string', // cast to string
        'drawing_object' => 'array', // json
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
     * Khai báo quan hệ n-1 với bảng cvedixrt_instance
     *
     * @return BelongsTo
     */
    public function instance(): BelongsTo
    {
        return $this->belongsTo(CvedixrtInstanceModel::class, 'cvedixrt_instance_id');
    }

    /*
     * $objects = $model->detected_object; // array từ DB
     *
     *   $objectEnums = array_map(fn($type) => DetectedObject::from($type), $objects);
     */
}
