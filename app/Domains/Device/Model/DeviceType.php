<?php declare(strict_types=1);

namespace App\Domains\Device\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DeviceType extends Model
{
    use HasFactory;

    protected $table = 'device_type';

    public const TABLE = 'device_type';

    public const PRIMARY_KEY = 'id';

    public const FOREIGN_KEY = 'device_type_id';

    protected $fillable = ['name', 'description', 'alias'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'device_type_id');
    }
}
