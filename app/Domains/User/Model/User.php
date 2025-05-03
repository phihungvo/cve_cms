<?php

declare(strict_types=1);

namespace App\Domains\User\Model;

use App\Domains\User\Role\Enum\RoleEnum;
use App\Domains\User\Role\Model\UserRole;
use App\Domains\UserGroup\Model\GroupModel;
use App\Domains\UserGroup\Model\UserGroupModel;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Domains\CoreApp\Model\ModelAbstract;
use App\Domains\Language\Model\Language as LanguageModel;
use App\Domains\Timezone\Model\Timezone as TimezoneModel;
use App\Domains\User\Model\Builder\User as Builder;
use App\Domains\User\Model\Collection\User as Collection;
use App\Domains\User\Model\Traits\Preferences as PreferencesTrait;
use App\Domains\User\Test\Factory\User as TestFactory;
use App\Domains\UserSession\Model\UserSession as UserSessionModel;
use App\Domains\User\Role\Model\Role as RoleModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Domains\User\Enterprise\Model\Enterprise as EnterpriseModel;
use App\Domains\Device\Model\Device as DeviceModel;

/**
 * @property int|null $enterprise_id
 * @property string $phone
 * @property string $api_key_full
 * @property array $preferences
 */
class User extends ModelAbstract implements Authenticatable
{
    use AuthenticatableTrait;
    use HasFactory;
    use HasRoles;
    use PreferencesTrait;

    /**
     * @var string
     */
    protected $table = 'user';

    /**
     * @const string
     */
    public const TABLE = 'user';

    /**
     * @const string
     */
    public const FOREIGN = 'user_id';

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'preferences' => 'array',
        'telegram' => 'array',
        'enabled' => 'boolean',
        'root' => 'boolean',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = ['password'];

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'api_key',
        'api_key_prefix',
        'api_key_enabled',
        'preferences',
        'admin',
        'admin_mode',
        'manager',
        'manager_mode',
        'enabled',
        'language_id',
        'timezone_id',
        'enterprise_id',
        'root',
    ];

    /**
     * @param array $models
     *
     * @return Collection
     */
    public function newCollection(array $models = []): Collection
    {
        return new Collection($models);
    }

    /**
     * @param \Illuminate\Database\Query\Builder $query
     *
     * @return Builder
     */
    public function newEloquentBuilder($query): Builder
    {
        return new Builder($query);
    }

    /**
     * @return TestFactory
     */
    protected static function newFactory(): TestFactory
    {
        return TestFactory::new();
    }

    /**
     * @return BelongsTo
     */
    public function language(): BelongsTo
    {
        return $this->belongsTo(LanguageModel::class, LanguageModel::FOREIGN);
    }

    /**
     * @return HasMany
     */
    public function sessions(): HasMany
    {
        return $this->hasMany(UserSessionModel::class, static::FOREIGN);
    }

    /**
     * @return BelongsTo
     */
    public function timezone(): BelongsTo
    {
        return $this->belongsTo(TimezoneModel::class, TimezoneModel::FOREIGN);
    }

    /**
     * @return bool
     */
    public function adminMode(): bool
    {
        return $this->admin && $this->admin_mode;
    }

    /**
     * @return bool
     */
    public function managerMode(): bool
    {
        return $this->manager && $this->manager_mode;
    }

    /**
     * Check if user is root
     *
     * @return bool
     */
    public function isRoleRoot(): bool
    {
        return $this->hasRole(RoleEnum::ROOT->value);
    }

    /**
     * Check if user is owner
     *
     * @return bool
     */
    public function isOwner(): bool
    {
        return $this->hasRole(RoleEnum::OWNER->value) && $this->hasEnterprise($this->enterprise_id);
    }

    /**
     * Thêm quan hệ đến Device
     *
     * @return HasMany
     */
    public function devices(): HasMany
    {
        return $this->hasMany(DeviceModel::class, 'user_id');
    }

    // Thêm quan hệ đến Role
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(
            RoleModel::class,
            UserRole::TABLE,
            User::FOREIGN,
            RoleModel::FOREIGN
        )
            ->withTimestamps()
            // ->select('role.id');
            ->select('role.id', 'role.name', 'role.alias');
    }

    /**
     * Kiểm tra nếu user có vai trò cụ thể
     *
     * @param string $roleName
     *
     * @return bool
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles->contains('name', $roleName);
    }

    public function hasRoleFeatureAccess(string $featureName): bool
    {
        return $this->roles()->whereHas('roleFeature.feature', function ($query) use ($featureName) {
            $query->where('alias', $featureName);
        })->exists();
    }

    /**
     * Quan hệ: Một User chỉ thuộc một doanh nghiệp (One-to-Many)
     */
    public function enterprise(): BelongsTo
    {
        return $this->belongsTo(EnterpriseModel::class, EnterpriseModel::FOREIGN);
    }

    /**
     * Kiểm tra nếu User thuộc một Enterprise cụ thể
     *
     * @param int|null $enterpriseId
     *
     * @return bool
     */
    public function hasEnterprise(?int $enterpriseId): bool
    {
        if (!$enterpriseId) {
            return false;
        }

        return $this->enterprise_id === $enterpriseId;
    }

    /**
     * Kiểm tra xem user có quyền root hay không
     *
     * @return bool
     */
    public function isRoot(): bool
    {
        // Kiểm tra dựa trên quan hệ, xem user có role 'root' hay không
        return $this->roles()->where('name', 'root')->exists();
        // Hoặc nếu muốn dùng cột root trong bảng users:
        // return $this->root === 1;
    }

    /**
     * Một User có thể thuộc nhiều Campaign
     *
     * @return BelongsToMany
     */
    public function campaigns(): BelongsToMany
    {
        return $this->belongsToMany(
            \App\Domains\Campaign\Model\Campaign::class,
            'user_campaign',
            'user_id',
            'campaign_id'
        )->withTimestamps();
    }

    /**
     * Một User có thể thuộc nhiều Group, quan hệ n-n
     *
     * @return BelongsToMany
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            GroupModel::class,
            UserGroupModel::TABLE,
            self::FOREIGN,
            GroupModel::FOREIGN_KEY
        )->withTimestamps();
    }
}
