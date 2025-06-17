<?php declare(strict_types=1);

namespace App\Domains\Cvedixrt\Event\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Cvedixrt\Instance\Model\CvedixrtInstanceRuleModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\Cvedixrt\Event\Model\Builder\EventBuilder;
use App\Domains\Cvedixrt\Event\Model\Collection\EventCollection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CvedixrtEventModel extends ModelAbstract
{
    use HasFactory;

    /**
     * @const string
     */
    const PRIMARYKEY = 'id';

    /**
     * @var string
     */
    protected $table = 'cvedixrt_event';

    /**
     * @const string
     */
    public const TABLE = 'cvedixrt_event';

    /**
     * @const string
     */
    public const FOREIGN_KEY = 'cvedixrt_event_id';

    public $timestamps = true;

    protected $fillable = [
        'uuid',
        'image_url',
        'video_url',
        'detected_object',
        'event_name',
        'event_value',
        'event_type',
        'instance_rule_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    /**
     * Create a custom collection instance.
     *
     * @param array $models
     *
     * @return EventCollection
     */
    public function newCollection(array $models = []): EventCollection
    {
        return new EventCollection($models);
    }

    /**
     * Create a custom builder instance.
     *
     * @param $query
     *
     * @return EventBuilder
     */
    public function newEloquentBuilder($query): EventBuilder
    {
        return new EventBuilder($query);
    }

    /**
     * Define the relationship with the CvedixrtInstanceRuleModel.
     *
     * @return BelongsTo
     */
    public function instanceRule(): BelongsTo
    {
        return $this->belongsTo(
            CvedixrtInstanceRuleModel::class,
            CvedixrtInstanceRuleModel::FOREIGN_KEY
        );
    }
}
