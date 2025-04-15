<?php

namespace App\Domains\CamCloud\Model;

use App\Domains\CamCloud\Model\Builder\CameraBuilder;
use App\Domains\CamCloud\Model\Collection\CameraCollection;
use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Device\Model\Device;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Camera extends ModelAbstract
{
    use HasFactory;

    protected $table = 'camera';

    public const TABLE = 'camera';

    public const PRIMARY = 'id';

    public const FOREIGN = 'device_id';

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
     * Một camera thuộc về một device
     *
     * @return BelongsTo
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    /**
     * Tạo một instance của Collection tùy chỉnh, thay vì sử dụng Collection mặc định của Eloquent.
     *  Không cần gọi trực tiếp phương thức này, Laravel sẽ tự động gọi khi cần thiết.
     *
     * @param array $models
     *
     * @return CameraCollection
     *
     * @see \Illuminate\Database\Eloquent\Model::newCollection()
     *
     * @override
     */
    public function newCollection(array $models = []): CameraCollection
    {
        return new CameraCollection($models);
    }

    /**
     * Tạo một instance của Eloquent builder tùy chỉnh, thay vì sử dụng Builder mặc định của Eloquent.
     *  Không cần gọi trực tiếp phương thức này, Laravel sẽ tự động gọi khi cần thiết.
     *
     * @param $query
     *
     * @return CameraBuilder
     *
     * @see \Illuminate\Database\Eloquent\Model::newEloquentBuilder()
     *
     * @override
     */
    public function newEloquentBuilder($query): CameraBuilder
    {
        return new CameraBuilder($query);
    }
}
