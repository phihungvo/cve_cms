<?php declare(strict_types=1);

namespace App\Domains\User\Permission\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Role\Model\Role;

class Permission extends ModelAbstract
{
    use HasFactory, SoftDeletes;
    public $timestamps = true;

    protected $table = 'permission';

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
        'menu_route_name',
        'menu_route_uri',
        'is_menu',
        'menu_name',
        'menu_icon',
        'parent_id',
    ];

    public function getNameAttribute(): ?string
    {
        return $this->role?->name;
    }
}