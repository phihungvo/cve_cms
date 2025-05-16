<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\License\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\User\Enterprise\EService\Model\EService;
class License extends ModelAbstract
{
    use HasFactory, SoftDeletes;

    protected $table = 'license';

    public $timestamps = true;

    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'id' => 'integer',
        'service_id' => 'integer',
        'enterprise_id' => 'integer',
        'max_users' => 'integer',
        'max_devices' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'service_id',
        'enterprise_id',
        'license_type',
        'max_users',
        'max_devices',
        'start_date',
        'end_date',
        'status',
        'license_key',
    ];

    public function service()
    {
        return $this->belongsTo(EService::class, 'service_id');
    }

    public function enterprise()
    {
        return $this->belongsTo(Enterprise::class, 'enterprise_id');
    }
}
