<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserGroupModel extends ModelAbstract
{
    use HasFactory;

    protected $table = 'user_group';

    public const TABLE = 'user_group';

    public const PRIMARY_KEY = 'id';

    protected $fillable = [
        'user_id',
        'group_id',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(GroupModel::class, 'group_id');
    }
}
