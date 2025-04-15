<?php declare(strict_types=1);

namespace App\Domains\Device\Model;

use App\Domains\CamCloud\Model\Builder\CameraBuilder;
use App\Domains\CamCloud\Model\Collection\CameraCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\CoreApp\Model\ModelAbstract;

class Camera extends ModelAbstract
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'camera';

    /**
     * @const string
     */
    public const TABLE = 'camera';

    /**
     * @const string
     */
    public const FOREIGN = 'device_id';

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'device_id',
        'description',
        'model',
        'serial',
        'uri',
        'location',
        'resolution',
        'created_at',
        'updated_at',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function newCollection(array $models = []): CameraCollection
    {
        return new CameraCollection($models);
    }

    public function newEloquentBuilder($query): CameraBuilder
    {
        return new CameraBuilder($query);
    }
}
