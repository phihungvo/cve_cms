<?php declare(strict_types=1);

namespace App\Domains\User\Enterprise\EService\Model;
use Illuminate\Database\Eloquent\Model;
use App\Domains\User\Permission\Model\Permission;

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
