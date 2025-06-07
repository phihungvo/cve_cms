<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Model;
use App\Domains\User\Permission\Model\Permission;

use Illuminate\Database\Eloquent\Model;

class RolePermission extends Model
{
    protected $table = 'role_permission';

    protected $fillable = [
        'role_id',
        'permission_id',
    ];

    public $timestamps = false;

    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_id');
    }
}
