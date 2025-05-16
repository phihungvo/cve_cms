<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Role\Model\Role;

class EService extends ModelAbstract
{
    use HasFactory, SoftDeletes;
    public $timestamps = true;

    protected $table = 'service';

    protected $dates = ['deleted_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
        'value' => 'integer',
    ];

    protected $fillable = [
        'name',
        'alias',
        'description',
        'enterprise_id',
        'pricing_model',
        'price',
        'billing_cycle',
        'max_unit',
        'note',
    ];

    public function getNameAttribute(): ?string
    {
        return $this->role?->name;
    }
}