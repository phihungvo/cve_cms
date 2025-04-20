<?php

namespace App\Domains\Vehicle\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Domains\Campaign\Media\Model\Media;
use App\Domains\Device\Model\Device;
use App\Domains\User\Enterprise\Model\Enterprise;

use App\Domains\Vehicle\Model\Vehicle;

class VehicleImageReport extends Model
{
    use SoftDeletes;

    protected $table = 'vehicle_image_report';

    protected $fillable = [
        'media_id',
        'device_id',
        'vehicle_id',
        'minio_url',
        'latitude',
        'longitude',
        'minio_bucket',
        'enterprise_id',
        'target',
        'source_type',
        'label'
    ];

    protected $casts = [
        'media_id' => 'integer',
        'device_id' => 'integer',
        'vehicle_id' => 'integer',
        'latitude' => 'float',
        'longitude' => 'float',
        'enterprise_id' => 'integer',
        'target' => 'integer',
        'source_type' => 'string',
        'label' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function media()
    {
        return $this->belongsTo(Media::class, 'media_id');
    }

    public function device()
    {
        return $this->belongsTo(Device::class, 'device_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }
}