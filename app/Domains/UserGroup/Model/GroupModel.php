<?php declare(strict_types=1);

namespace App\Domains\UserGroup\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Enterprise\Model\Enterprise;
use App\Domains\UserGroup\Model\Builder\GroupBuilder;
use App\Domains\UserGroup\Model\Collection\GroupCollection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Model\Builder\GroupBuilder as Builder;
use Model\Collection\GroupCollection as Collection;

/**
 * @property string $name
 * @property string|null $description
 * @property int|null $enterprise_id
 */
class GroupModel extends ModelAbstract
{
    use HasFactory;
    use SoftDeletes;

    public $table = 'group';

    public const TABLE = 'group';

    public const PRIMARY_KEY = 'id';

    public const FOREIGN_KEY = 'group_id';

    protected $fillable = [
        'name',
        'description',
        'enterprise_id',
    ];

    // khai báo quan hệ n-1 với enterprise
    // 1 group chỉ thuộc về 1 enterprise
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(Enterprise::class, Enterprise::FOREIGN_KEY, Enterprise::PRIMARY);
    }

    public function userGroups(): HasMany
    {
        return $this->hasMany(UserGroupModel::class, 'group_id', self::PRIMARY_KEY);
    }

    /**
     * @param array $models
     *
     * @return Collection
     */
    public function newCollection(array $models = []): GroupCollection
    {
        return new GroupCollection($models);
    }

    /**
     * @param $query
     *
     * @return Builder
     */
    public function newEloquentBuilder($query): GroupBuilder
    {
        return new GroupBuilder($query);
    }
}
