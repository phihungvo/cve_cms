<?php declare(strict_types=1);

namespace App\Domains\Device\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Domains\CoreApp\Model\ModelAbstract;

class DeviceLog extends ModelAbstract
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'device_log';

    /**
     * @const string
     */
    public const TABLE = 'device_log';

    // /**
    //  * @const string
    //  */
    // public const FOREIGN = 'device_id';

    /**
     * @var array
     */
    protected $fillable = [
        'serial',
        'type',
        'description',
        // 'data',
        // 'status',
        // 'error',
        'created_at',
        // 'updated_at',
    ];

    protected $casts = [
        // 'data' => 'array',
        'created_at' => 'datetime',
        // 'updated_at' => 'datetime',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'serial', 'serial');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function deviceStatus(): BelongsTo
    {
        return $this->belongsTo(DeviceStatus::class, 'serial', 'serial');
    }

    /**
     * Log a device event based on DeviceStatus and Device
     *
     * @param Device $device
     * @param DeviceStatus $deviceStatus
     * @param string $type
     * @param string|null $description
     *
     * @return static
     */
    public static function logEvent(Device $device, DeviceStatus $deviceStatus, string $type, ?string $description = null): self
    {
        return self::create([
            'serial' => $device->serial,
            'type' => $type,
            'description' => $description,
            // 'data' => $deviceStatus->data,
            // 'status' => $deviceStatus->status ?? null,
            // 'error' => $deviceStatus->error ?? null,
        ]);
    }
}
