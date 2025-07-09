<?php declare(strict_types=1);

namespace App\Domains\Device\Model;

use App\Domains\Core\Model\ModelAbstract;
use App\Domains\Device\Model\Builder\DeviceCvedixrtEventBuilder as Builder;
use App\Domains\Device\Model\Collection\DeviceCvedixrtEventCollection as Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property string $uuid
 * @property string $image_url
 * @property string $video_url
 * @property string $detected_object
 * @property string $event_name
 * @property string $event_value
 * @property string $event_type
 * @property int $instance_rule_id
 * @property int $device_id
 */
class DeviceCvedixrtEvent extends ModelAbstract
{
    protected $table = 'device_cvedixrt_event';

    public string $PRIMARY_KEY = 'id';

    public string $FOREIGN_KEY = 'instance_rule_id';

    protected $fillable = [
        'uuid',
        'image_url',
        'video_url',
        'detected_object',
        'event_name',
        'event_value',
        'event_type',
        'instance_rule_id',
        'device_id',
    ];

    protected function casts(): array
    {
        return [
            'created_at' => 'timestamp',
        ];
    }

    /**
     * @param array $models
     *
     * @return Collection
     */
    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
    }

    /**
     * @param $query
     *
     * @return Builder
     */
    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }

    public function instanceRule(): BelongsTo
    {
        return $this->belongsTo(DeviceCvedixrtInstanceRule::class, 'instance_rule_id');
    }

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }
}
