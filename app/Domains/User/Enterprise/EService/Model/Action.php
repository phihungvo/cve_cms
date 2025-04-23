<?php declare(strict_types=1);

namespace App\Domains\User\Permission\Model;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $table = 'actions';

    protected $fillable = [
        'name',
        'description',
    ];

    public function permissions()
    {
        return $this->hasMany(Permission::class, 'action_id');
    }
}
