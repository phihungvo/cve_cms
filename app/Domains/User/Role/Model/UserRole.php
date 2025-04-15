<?php declare(strict_types=1);

namespace App\Domains\User\Role\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Domains\CoreApp\Model\ModelAbstract;

class UserRole extends ModelAbstract
{
    use HasFactory;

    public $timestamps = true;

    protected $table = 'user_role';

    public const TABLE = 'user_role';

    protected $dates = ['deleted_at'];

    protected $casts = [
        'created_at' => 'datetime',
        'value' => 'integer',
    ];

    protected $fillable = [
        'user_id',
        'enterprise_id',
        'role_id',
    ];

    public function getNameAttribute(): ?string
    {
        return $this->role?->name;
    }
}
