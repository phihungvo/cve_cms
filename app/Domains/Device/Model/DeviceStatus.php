<?php declare(strict_types=1);

namespace App\Domains\Device\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\CoreApp\Model\ModelAbstract;

class DeviceStatus extends ModelAbstract
{
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'device_status';

    /**
     * @var string
     */
    protected $primaryKey = 'serial'; // Serial làm khóa chính

    /**
     * @var bool
     */
    public $incrementing = false; // Không tự tăng id

    /**
     * @var string
     */
    protected $keyType = 'string'; // Kiểu khóa chính (serial)

    /**
     * @const string
     */
    public const TABLE = 'device_status';

    public $timestamps = true;
    /**
     * @const string
     */
    // public const FOREIGN = 'device_id';

    /**
     * @var array
     */
    protected $fillable = [
        'serial',
        'data',
        // 'error',
        'created_at',
        'updated_at',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'data' => 'array', // Tự động mã hóa/giải mã JSON
    ];
}
