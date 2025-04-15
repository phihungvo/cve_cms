<?php

declare(strict_types=1);

namespace App\Domains\User\Role\Model;

use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\User\Model\User as UserModel;
use App\Domains\User\Permission\Model\Permission;
use App\Domains\User\Role\Model\Collection\Role as Collection;
use App\Domains\User\Role\Model\Traits\TypeFormat;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use \App\Domains\User\Role\Model\Builder\Role as Builder;
use App\Domains\CoreApp\Model\Traits\Gis as GisTrait;

class Role extends ModelAbstract
{
    use GisTrait;
    use HasFactory;
    use SoftDeletes;
    use TypeFormat;

    /**
     * @var string
     */
    protected $table = 'role';

    /**
     * @const string
     */
    public const TABLE = 'role';

    /**
     * @const string
     */
    public const FOREIGN = 'role_id';

    public $timestamps = true;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'enterprise_id',
        'alias',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'soft_deleted_at' => 'datetime', // Thêm cột soft_deleted_at vào đây
    ];

    /**
     * Quan hệ: Một Role có nhiều User thông qua `user_roles`
     *
     * @return BelongsToMany
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            UserModel::class,
            'user_roles',
            'role_id',
            'user_id'
        )->withTimestamps();
    }

    /**
     * Quan hệ: Một Role có nhiều Permissions thông qua RolePermissions (n-n).
     *
     * @return BelongsToMany
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'role_permission',
            'role_id',
            'permission_id'
        )->withTimestamps();
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param array $models
     *
     * @return Collection
     */
    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
    }

    /**
     * Create a new Eloquent query builder for the model.
     *
     * @param $query
     *
     * @return Builder
     */
    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }
}
