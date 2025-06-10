<?php declare(strict_types=1);

namespace App\Domains\Device\Model;

use App\Domains\Core\Model\ModelAbstract;
use App\Domains\Device\Model\Builder\DeviceCvedixrtEventBuilder as Builder;
use App\Domains\Device\Model\Collection\DeviceCvedixrtEventCollection as Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceCvedixrtEvent extends ModelAbstract
{
    protected $table = 'device_cvedixrt_event';

    public string $PRIMARY_KEY = 'id';

    public string $FOREIGN_KEY = 'instance_rule_id';

    protected $fillable = [
        'uuid',
        'image_url',
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
