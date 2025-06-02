<?php declare(strict_types=1);

namespace App\Domains\DeviceGroup\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Device\Model\Device;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeviceGroupMap extends ModelAbstract
{
    protected $table = 'device_group_map';

    public const TABLE = 'device_group_map';

    public const PRIMARY = 'id';

    public const FOREIGN = 'device_group_map_id';

    protected $fillable = [
        'device_id',
        'device_group_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class);
    }

    public function deviceGroup(): BelongsTo
    {
        return $this->belongsTo(DeviceGroupModel::class, 'device_group_id');
    }
}
