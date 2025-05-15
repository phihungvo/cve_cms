<?php declare(strict_types=1);

namespace App\Domains\VehicleGroup\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Vehicle\Model\Vehicle;

class VehicleGroupMap extends ModelAbstract
{
    protected $table = 'vehicle_group_map';

    public const TABLE = 'vehicle_group_map';

    public const PRIMARY = 'id';

    public const FOREIGN = 'vehicle_group_map_id';

    protected $fillable = [
        'vehicle_id',
        'vehicle_group_id',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function vehicleGroup(): BelongsTo
    {
        return $this->belongsTo(VehicleGroupModel::class, VehicleGroupModel::FOREIGN);
    }
}
