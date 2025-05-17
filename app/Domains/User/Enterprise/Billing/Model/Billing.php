<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\Billing\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\CoreApp\Model\ModelAbstract;

class Billing extends ModelAbstract
{
    use HasFactory, SoftDeletes;

    protected $table = 'billing_record';

    public $timestamps = true;

    protected $dates = ['start_date', 'end_date', 'created_at', 'updated_at', 'deleted_at'];

    protected $casts = [
        'id' => 'integer',
        'name' => 'string',
        'license_id' => 'integer',
        'start_date' => 'date',
        'end_date' => 'date',
        'usage_unit' => 'integer',
        'payment_status' => 'string',
        'price' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $fillable = [
        'name',
        'license_id',
        'start_date',
        'end_date',
        'usage_unit',
        'payment_status',
        'price',
    ];
}
